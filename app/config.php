<?php
// Dados institucionais da GastriClin. Fonte única: altere aqui e o site inteiro se atualiza.
// Coletado do site atual (gastriclin.com.br) e da Doctoralia em 12/10/2026.

const SITE = [
    'nome' => 'GastriClin',
    'nome_completo' => 'GastriClin – Centro de Medicina Digestiva',
    'slogan' => 'Excelência na saúde gastrointestinal',
    'url' => 'https://gastriclin.com.br',
    'cnpj' => '37.904.453/0001-52',

    'endereco' => [
        'rua' => 'Rua Dom Sebastião Leme, 1115',
        'complemento' => '1º andar – Prédio da Prontoclínica de Fortaleza',
        'bairro' => 'Fátima',
        'cidade' => 'Fortaleza',
        'uf' => 'CE',
        'cep' => '60050-160',
    ],

    'telefone' => [
        'exibicao' => '(85) 98960-5143',
        'e164' => '+5585989605143',
    ],
    'email' => 'administrativo@gastriclin.com.br',

    // PENDENTE: horário de funcionamento. Exemplo: [['dias' => 'Segunda a sexta', 'horas' => '7h às 18h']]
    'horario' => [],

    'links' => [
        'doctoralia' => 'https://www.doctoralia.com.br/clinicas/gastriclin',
        'instagram' => 'https://www.instagram.com/gastriclin/',
        'facebook' => 'https://www.facebook.com/profile.php?id=61575290969572',
        'google_maps' => 'https://maps.app.goo.gl/vTRcRVND5ANxgyjUA',
        'google_perfil' => 'https://g.co/kgs/N7MiVW5',
        'resultados' => 'https://www.resultados.app.br/', // sistema de laudos e imagens (externo, sem integração com o site)
        'waze' => 'https://waze.com/ul?q=Rua%20Dom%20Sebasti%C3%A3o%20Leme%2C%201115%2C%20Fortaleza&navigate=yes',
    ],

    'whatsapp' => [
        'numero' => '5585989605143',
        'mensagem_padrao' => 'Olá! Vim pelo site da GastriClin e gostaria de agendar um atendimento.',
    ],

    // Responsável técnica informada pela clínica em 12/10/2026. 'registro' fica vazio (a clínica optou por não exibir o COREN).
    'responsavel_tecnico' => [
        'nome' => 'Valdeline Sousa',
        'profissao' => 'Enfermeira',
        'registro' => '',
    ],

    // Google Analytics 4. Vazio = nenhum script de rastreamento e nenhum banner de cookies.
    'ga4_id' => '',
];

// Redirecionamentos das URLs antigas do WordPress (301 = permanente, 302 = temporário)
const REDIRECIONAMENTOS = [
    '/dra-pamela-de-alencar-fortaleza-sousa/' => ['/dra-ana-beatriz-gordiano-vasconcelos-valente/', 301],
    '/dr-matheus-brito/' => ['/dr-matheus-castelo-branco/', 301],
    '/equipe/' => ['/clinico/', 301],
    '/links/' => ['/bio/', 301],
    '/links-2/' => ['/bio/', 301],
    '/feed/' => ['/blog/', 301],
    '/comments/feed/' => ['/blog/', 301],
    // Profissionais fora do corpo clínico atual (PENDENTE: confirmar com a clínica)
    '/dra-isabelle-balduino-dantas/' => ['/clinico/', 302],
    '/dra-talita-de-lima-aquino-vilarinho/' => ['/clinico/', 302],
    '/dra-izabella-dornellas/' => ['/clinico/', 302],
    '/dr-filipe-herson/' => ['/clinico/', 301], // saiu do site em 19/10/2026 a pedido da clínica
];
// Prefixos de arquivos automáticos do WordPress que vão para o blog
const REDIRECIONAMENTOS_PREFIXO = ['/category/', '/tag/', '/author/'];
