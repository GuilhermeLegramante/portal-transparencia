     @php
         $columns = [
             ['label' => 'Exercício', 'icone' => 'fa fa-calendar', 'align' => 'text-center'],
             ['label' => 'Vlr. Orçado', 'icone' => 'fa fa-coins', 'align' => 'text-end'],
             ['label' => 'Vlr. Corrigido', 'icone' => 'fa fa-edit', 'align' => 'text-end'],
             ['label' => 'Vlr. Executado', 'icone' => 'fa fa-check-circle', 'align' => 'text-end'],
             ['label' => 'Vlr. Restos', 'icone' => 'fa fa-history', 'align' => 'text-end'],
             ['label' => 'Ação', 'icone' => '', 'align' => 'text-center'],
         ];
     @endphp

     <x-tabela-transparencia titulo="Resumo da movimentação por exercício" cor="primary" :colunas="$columns">
         @forelse($resumoAnual as $resumo)
             <tr>
                 <td class="text-center fw-bold">{{ $resumo->exercicio }}</td>

                 {{-- Valor Orçado --}}
                 <td class="text-end text-muted">
                     R$ {{ number_format($resumo->valor_orcado ?? 0, 2, ',', '.') }}
                 </td>

                 {{-- Valor Corrigido --}}
                 <td class="text-end">
                     R$ {{ number_format($resumo->valor_corrigido ?? 0, 2, ',', '.') }}
                 </td>

                 {{-- Valor Executado (Destaque em negrito) --}}
                 <td class="text-end fw-bold text-primary">
                     R$ {{ number_format($resumo->valor_executado ?? 0, 2, ',', '.') }}
                 </td>

                 {{-- Valor de Restos --}}
                 <td class="text-end">
                     R$ {{ number_format($resumo->valor_restos ?? 0, 2, ',', '.') }}
                 </td>

                 <td class="text-center">
                     <a href="{{ route($detailsRoute, $resumo->exercicio) }}"
                         class="btn btn-action-view btn-sm shadow-sm">
                         <i class="fa fa-eye me-1"></i> Detalhes
                     </a>
                 </td>
             </tr>
         @empty
             <tr>
                 {{-- Atualizado o colspan para 6 colunas --}}
                 <td colspan="6" class="text-center py-4">
                     <div class="text-muted">
                         <i class="fa fa-info-circle me-1"></i> Nenhum registro encontrado para este cliente.
                     </div>
                 </td>
             </tr>
         @endforelse
     </x-tabela-transparencia>
     @include('layouts.partials.back')
