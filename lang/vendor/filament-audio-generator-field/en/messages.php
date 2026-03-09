<?php

return [
    'labels' => [
        'generate-audio' => 'Generate using AI',
    ],

    'form' => [
        'fields' => [
            'prompt' => 'Prompt',
            'prompt-placeholder' => 'example: `I love my dog, his name is Bob`. Don\'t forget to use correct accentuation.',

            'language' => 'Language',
            'language-hint' => 'Select the audio language.',
            'voice' => 'Voice Style',
            'voice-hint' => 'Select the voice style for the audio.',
        ],

        'errors' => [
            'no-audios-generated' => 'No audio was generated. Please try again.',
        ]
    ],

    'modals' => [
        'generate-an-audio' => [
            'title' => 'Audio Generation',
            'description' => 'Describe in detail the audio you want to generate.<br />Please wait while the audio is being generated.',
            'generate' => 'Generate',
            'generating' => 'Generating...',
            'add-generated' => 'Add generated audio',
            'cancel' => 'Cancel',
            'select' => 'Select',
            'uploading' => 'Uploading...',
        ]
    ]
];
