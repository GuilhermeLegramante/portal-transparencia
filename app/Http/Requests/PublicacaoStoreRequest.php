<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicacaoStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tipo_publicacao' => 'required|in:prestacao,geral',
            'descricao'       => 'required|string|max:255',
            'exercicio'       => 'required|numeric|digits:4',
            'datahora'        => 'required|date',
            'arquivo'         => 'required|file|mimes:pdf,doc,docx,xls,xlsx,zip|max:20480', // Máx 20MB

            // Obrigatório apenas se for Prestação de Contas
            'categoria_texto' => 'required_if:tipo_publicacao,prestacao|nullable|string|max:100',

            // Opcional para Publicações Gerais (Tags)
            'tags'            => 'array|nullable',
        ];
    }

    public function messages()
    {
        return [
            'tipo_publicacao.required' => 'Selecione o tipo de publicação.',
            'descricao.required'       => 'A descrição é obrigatória.',
            'exercicio.required'       => 'O exercício é obrigatório.',
            'datahora.required'        => 'A data e hora são obrigatórias.',
            'arquivo.required'         => 'O arquivo do documento é obrigatório.',
            'arquivo.mimes'            => 'O arquivo deve ser um documento válido (PDF, Word, Excel ou ZIP).',
            'categoria_texto.required_if' => 'Informe a categoria para a Prestação de Contas.',
        ];
    }
}
