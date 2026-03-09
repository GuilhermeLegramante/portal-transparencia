<?php

return [
    'labels' => [
        'generate-audio' => 'Converta um texto em áudio para análise da pronúncia correta das palavras.',
    ],

    'form' => [
        'fields' => [
            'prompt' => 'Texto',
            'prompt-placeholder' => 'exemplo: `I like eating fruit`. Não se esqueça de manter a escrita com acentuação correta e escrever o texto no idioma escolhido.',

            'language' => 'Linguagem',
            'language-hint' => 'Selecione a linguagem do áudio.',
            'voice' => 'Estilo de Voz',
            'voice-hint' => 'Selecione o estilo da voz do áudio.',
        ],

        'errors' => [
            'no-audios-generated' => 'Nenhum áudio foi gerado. Por favor tente novamente.',
        ]
    ],

    'modals' => [
        'generate-an-audio' => [
            'title' => 'Geração de Áudio',
            'description' => 'Descreva detalhadamente o áudio que deseja gerar.<br />Aguarde enquanto o áudio está sendo gerado.',
            'generate' => 'Gerar',
            'generating' => 'Gerando...',
            'add-generated' => 'Adicionar áudio gerado',
            'cancel' => 'Cancelar',
            'select' => 'Selecionar',
            'uploading' => 'Enviando...',
        ]
    ]
];
