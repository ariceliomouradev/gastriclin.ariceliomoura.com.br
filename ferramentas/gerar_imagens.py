"""Gera as imagens otimizadas (WebP em vários tamanhos) da versão PHP e o manifesto app/dados/imagens.json.

Uso:  python ferramentas/gerar_imagens.py   (na raiz do repositório)
Para uma foto nova ou trocada: coloque o original em originais/<grupo>/<nome>.<ext> e rode de novo.
(Capas do blog enviadas pelo painel /admin são otimizadas pelo próprio PHP.)
"""
import glob
import hashlib
import json
import os
from PIL import Image

V2 = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))  # raiz do repositório
ORIGINAIS = os.path.join(V2, 'originais')
SAIDA = os.path.join(V2, 'assets', 'img')
MANIFESTO = os.path.join(V2, 'app', 'dados', 'imagens.json')

# grupo: (larguras, qualidade)
GRUPOS = {
    'clinica': ([400, 640, 720, 960, 1280], 78),
    'equipe': ([96, 280, 420, 560], 82),
    'servicos': ([360, 540, 720], 82),
    'convenios': ([160, 320], 90),
    'marca': ([170, 240, 340, 480], 92),
}
QUALIDADE_ESPECIAL = {'clinica/recepcao': 70}


def fontes():
    """Fotos originais em originais/<grupo>/<nome>.<ext>."""
    vistos = {}
    for base in (ORIGINAIS,):
        for grupo in GRUPOS:
            for f in glob.glob(os.path.join(base, grupo, '*')):
                if os.path.splitext(f)[1].lower() in ('.jpg', '.jpeg', '.png', '.webp'):
                    vistos[f'{grupo}/{os.path.splitext(os.path.basename(f))[0]}'] = f
    return vistos


manifesto = {}
for chave, arquivo in sorted(fontes().items()):
    grupo = chave.split('/')[0]
    larguras, qualidade = GRUPOS[grupo]
    qualidade = QUALIDADE_ESPECIAL.get(chave, qualidade)
    im = Image.open(arquivo)
    im = im.convert('RGBA') if im.mode in ('RGBA', 'LA', 'P') else im.convert('RGB')
    w, h = im.size
    feitas = []
    for lw in larguras:
        lw = min(lw, w)
        if lw in feitas:
            continue
        lh = round(h * lw / w)
        destino = os.path.join(SAIDA, f'{chave}-{lw}.webp')
        os.makedirs(os.path.dirname(destino), exist_ok=True)
        im.resize((lw, lh), Image.LANCZOS).save(destino, 'WEBP', quality=qualidade, method=6)
        feitas.append(lw)
    # "v" muda quando a foto original muda: entra no endereço (?v=...) para o navegador não mostrar a foto antiga do cache
    versao = hashlib.md5(open(arquivo, 'rb').read()).hexdigest()[:8]
    manifesto[chave] = {'w': w, 'h': h, 'larguras': feitas, 'v': versao}

os.makedirs(os.path.dirname(MANIFESTO), exist_ok=True)
json.dump(manifesto, open(MANIFESTO, 'w', encoding='utf-8'), ensure_ascii=False, indent=1)
total = sum(os.path.getsize(f) for f in glob.glob(os.path.join(SAIDA, '**', '*.webp'), recursive=True))
print(f'{len(manifesto)} imagens, {total // 1024} KB em WebP')
