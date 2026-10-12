<?php
// Consultas e exames. Textos do site atual, reorganizados.
// Conteúdo médico: revisar com o responsável técnico antes de publicar.
// 'imagem' = chave em app/dados/imagens.json; 'icone' = símbolo em app/componentes/icones.svg.

return [
    [
        'slug' => 'gastroenterologia',
        'tipo' => 'consulta',
        'nome' => 'Gastroenterologia',
        'nome_agendamento' => 'Consulta em Gastroenterologia',
        'icone' => 'estomago',
        'resumo' => 'Gastrite, refluxo, úlcera, dor abdominal, intestino, fígado e vesícula.',
        'seo' => [
            'title' => 'Gastroenterologista em Fortaleza | GastriClin',
            'description' => 'Consulta com gastroenterologista em Fortaleza: gastrite, refluxo, úlcera, dor abdominal e doenças do sistema digestivo. Agende online ou pelo WhatsApp.',
        ],
        'intro' => [
            'A Gastroenterologia cuida do diagnóstico e do tratamento das doenças do sistema digestivo: esôfago, estômago, intestinos, fígado, vesícula biliar e pâncreas.',
            'Na consulta, o médico investiga seus sintomas, solicita os exames necessários e indica o melhor tratamento para o seu caso.',
        ],
        'lista_titulo' => 'Quando procurar um gastroenterologista',
        'lista' => [
            [
                'titulo' => 'Gastrite',
                'texto' => 'Dor ou queimação no estômago, náuseas e desconforto depois das refeições.',
            ],
            [
                'titulo' => 'Refluxo (DRGE)',
                'texto' => 'Azia, queimação atrás do peito e, às vezes, tosse crônica ou rouquidão.',
            ],
            [
                'titulo' => 'Úlcera gástrica',
                'texto' => 'Lesão no estômago ou duodeno, muitas vezes ligada à bactéria H. pylori ou ao uso de anti-inflamatórios.',
            ],
            [
                'titulo' => 'Dor na parte de cima do abdome',
                'texto' => 'Epigastralgia, que pode ter várias causas, como gastrite, úlcera ou dispepsia.',
            ],
            [
                'titulo' => 'Alterações do intestino',
                'texto' => 'Síndrome do intestino irritável, constipação, diarreia crônica e intolerâncias alimentares.',
            ],
            [
                'titulo' => 'Fígado e vesícula',
                'texto' => 'Hepatites, gordura no fígado (esteatose) e cálculos na vesícula.',
            ],
        ],
        'destaques' => ['Consulta e exames no mesmo lugar', 'Atende diversos convênios', 'Equipe com endoscopistas'],
        'imagem' => 'servicos/gastroenterologia',
        'temas' => ['gastrite', 'refluxo', 'azia', 'estomago', 'estômago', 'dor', 'gases', 'intestino irritado', 'digest'],
    ],
    [
        'slug' => 'coloproctologia',
        'tipo' => 'consulta',
        'nome' => 'Coloproctologia',
        'nome_agendamento' => 'Consulta em Coloproctologia',
        'icone' => 'colon',
        'resumo' => 'Hemorroidas, fissura, constipação, sangramento e prevenção do câncer colorretal.',
        'seo' => [
            'title' => 'Coloproctologista em Fortaleza | GastriClin',
            'description' => 'Consulta com coloproctologista em Fortaleza: hemorroidas, fissura anal, constipação, sangramento e prevenção do câncer colorretal. Agende online ou pelo WhatsApp.',
        ],
        'intro' => [
            'A Coloproctologia cuida da prevenção, do diagnóstico e do tratamento das doenças do intestino grosso (cólon), do reto e do ânus.',
            'São problemas comuns e, muitas vezes, adiados por constrangimento. Uma avaliação no momento certo evita complicações e traz alívio mais rápido.',
        ],
        'lista_titulo' => 'Quando procurar um coloproctologista',
        'lista' => [
            [
                'titulo' => 'Hemorroidas',
                'texto' => 'Dor, sangramento e desconforto na região anal.',
            ],
            [
                'titulo' => 'Fissura anal',
                'texto' => 'Pequeno corte no ânus, com dor intensa e sangramento ao evacuar.',
            ],
            [
                'titulo' => 'Fístula e abscesso',
                'texto' => 'Infecções na região anal, com dor, inchaço e, às vezes, febre.',
            ],
            [
                'titulo' => 'Constipação intestinal',
                'texto' => 'Dificuldade persistente para evacuar.',
            ],
            [
                'titulo' => 'Incontinência fecal',
                'texto' => 'Perda involuntária de fezes, com impacto na qualidade de vida.',
            ],
            [
                'titulo' => 'Doença inflamatória intestinal',
                'texto' => 'Retocolite ulcerativa e doença de Crohn.',
            ],
            [
                'titulo' => 'Pólipos e câncer colorretal',
                'texto' => 'Acompanhamento e rastreamento para prevenção e diagnóstico precoce.',
            ],
        ],
        'destaques' => [
            'Atendimento reservado e acolhedor',
            'Colonoscopia na própria clínica',
            'Atende diversos convênios',
        ],
        'imagem' => 'servicos/coloproctologia',
        'temas' => ['procto', 'hemorroida', 'intestino', 'colonoscopia', 'prisão de ventre', 'colon', 'cólon'],
    ],
    [
        'slug' => 'endoscopia',
        'tipo' => 'exame',
        'nome' => 'Endoscopia Digestiva Alta',
        'nome_agendamento' => 'Endoscopia Digestiva Alta',
        'icone' => 'endoscopio',
        'resumo' => 'Avalia esôfago, estômago e duodeno. Realizada com sedação.',
        'seo' => [
            'title' => 'Endoscopia Digestiva em Fortaleza | GastriClin',
            'description' => 'Endoscopia digestiva alta em Fortaleza, com sedação, para avaliar esôfago, estômago e duodeno. Atendemos convênios. Agende online ou pelo WhatsApp.',
        ],
        'intro' => [
            'A endoscopia digestiva alta permite ver diretamente o esôfago, o estômago e o duodeno (início do intestino delgado), por meio de um tubo flexível com uma microcâmera.',
            'Na GastriClin, o exame é feito com sedação, em ambiente seguro e acolhedor.',
        ],
        'lista_titulo' => 'Quando o exame é indicado',
        'lista' => [
            [
                'titulo' => 'Azia, queimação ou refluxo frequente',
            ],
            [
                'titulo' => 'Dor ou desconforto na parte de cima do abdome',
            ],
            [
                'titulo' => 'Náuseas, vômitos persistentes ou dificuldade para engolir',
            ],
            [
                'titulo' => 'Suspeita de gastrite, úlcera ou infecção por H. pylori',
            ],
        ],
        'como_funciona' => [
            'O exame é feito com sedação e acompanhamento da equipe do início ao fim.',
            'A indicação e o preparo são orientados pelo médico e pela nossa equipe no agendamento.',
        ],
        'destaques' => ['Com sedação', 'Feito na própria clínica', 'Atende convênios'],
        'imagem' => 'servicos/endoscopia',
        'temas' => ['endoscopia', 'gastrite', 'refluxo', 'azia', 'exames'],
    ],
    [
        'slug' => 'colonoscopia',
        'tipo' => 'exame',
        'nome' => 'Colonoscopia',
        'nome_agendamento' => 'Colonoscopia',
        'icone' => 'intestino',
        'resumo' => 'Avalia todo o intestino grosso. Principal exame de prevenção do câncer de intestino.',
        'seo' => [
            'title' => 'Colonoscopia em Fortaleza | GastriClin',
            'description' => 'Colonoscopia em Fortaleza com sedação: prevenção do câncer de intestino e investigação de sangramento, dor e alterações intestinais. Agende online ou pelo WhatsApp.',
        ],
        'intro' => [
            'A colonoscopia permite visualizar todo o intestino grosso (cólon) e parte final do intestino delgado. Com uma câmera em um tubo flexível, o médico identifica pólipos, inflamações e tumores.',
            'O exame é feito com sedação e com preparo intestinal prévio, seguindo protocolos de conforto e segurança.',
        ],
        'lista_titulo' => 'Quando o exame é indicado',
        'lista' => [
            [
                'titulo' => 'Rastreamento do câncer de intestino a partir dos 45 anos',
            ],
            [
                'titulo' => 'Sangue nas fezes',
            ],
            [
                'titulo' => 'Dor abdominal, diarreia ou constipação persistentes',
            ],
            [
                'titulo' => 'Histórico familiar de câncer colorretal ou pólipos',
            ],
        ],
        'como_funciona' => [
            'Antes do exame é necessário um preparo intestinal, explicado pela nossa equipe no agendamento.',
            'O exame é feito com sedação. Se forem encontrados pólipos, eles podem ser retirados no mesmo procedimento (polipectomia), conforme avaliação médica.',
        ],
        'destaques' => ['Com sedação', 'Diagnóstica e terapêutica', 'Atende convênios'],
        'imagem' => 'servicos/colonoscopia',
        'temas' => ['colonoscopia', 'intestino', 'colon', 'cólon', 'cancer', 'câncer', 'exames'],
    ],
    [
        'slug' => 'polipectomia',
        'tipo' => 'exame',
        'nome' => 'Polipectomia',
        'nome_agendamento' => 'Polipectomia',
        'icone' => 'polipo',
        'resumo' => 'Retirada de pólipos durante a colonoscopia ou endoscopia, sem cortes.',
        'seo' => [
            'title' => 'Polipectomia em Fortaleza | GastriClin',
            'description' => 'Polipectomia em Fortaleza: retirada de pólipos durante a colonoscopia ou endoscopia, com sedação e sem cortes. Agende online ou pelo WhatsApp.',
        ],
        'intro' => [
            'A polipectomia é a retirada de pólipos, pequenas formações que surgem na parede do tubo digestivo, durante a colonoscopia ou a endoscopia.',
            'A maioria dos pólipos é benigna, mas alguns podem evoluir para câncer com o tempo. Por isso, retirá-los cedo é uma forma eficaz de prevenção.',
        ],
        'lista_titulo' => 'Como é o procedimento',
        'lista' => [
            [
                'titulo' => 'Feito durante a colonoscopia ou a endoscopia',
            ],
            [
                'titulo' => 'Com sedação',
            ],
            [
                'titulo' => 'Sem cortes e sem internação',
            ],
            [
                'titulo' => 'O material retirado é enviado para análise, conforme indicação médica',
            ],
        ],
        'destaques' => ['Com sedação', 'Sem cortes', 'Sem internação'],
        'imagem' => 'servicos/polipectomia',
        'temas' => ['colonoscopia', 'polipo', 'pólipo', 'cancer', 'câncer', 'intestino'],
    ],
    [
        'slug' => 'retossigmoidoscopia',
        'tipo' => 'exame',
        'nome' => 'Retossigmoidoscopia',
        'nome_agendamento' => 'Retossigmoidoscopia',
        'icone' => 'colon',
        'resumo' => 'Avalia o reto e o sigmoide. Preparo mais simples que o da colonoscopia.',
        'seo' => [
            'title' => 'Retossigmoidoscopia em Fortaleza | GastriClin',
            'description' => 'Retossigmoidoscopia em Fortaleza: exame do reto e do sigmoide para investigar sangramento, dor anal e alterações nas fezes. Agende online ou pelo WhatsApp.',
        ],
        'intro' => [
            'A retossigmoidoscopia é um exame endoscópico que avalia o reto e o segmento final do intestino grosso (sigmoide).',
            'Por ser mais rápido e ter preparo intestinal mais simples que a colonoscopia, é uma boa alternativa em casos selecionados pelo médico.',
        ],
        'lista_titulo' => 'Quando o exame é indicado',
        'lista' => [
            [
                'titulo' => 'Sangramento pelo reto',
            ],
            [
                'titulo' => 'Dor anal',
            ],
            [
                'titulo' => 'Alterações nas fezes',
            ],
            [
                'titulo' => 'Exame de triagem em algumas situações',
            ],
        ],
        'destaques' => ['Preparo mais simples', 'Exame rápido', 'Atende convênios'],
        'imagem' => 'servicos/retossigmoidoscopia',
        'temas' => ['procto', 'hemorroida', 'intestino', 'reto'],
    ],
];
