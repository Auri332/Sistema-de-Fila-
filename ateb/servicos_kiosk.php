<?php
function obterServicosKiosk(): array
{
    return [
        'Atendimento Geral' => 'G',
        'Atendimento de Isenção' => 'I',
        'Atendimento de Prioridade' => 'P',
    ];
}

function expandirServicosKiosk(array $servicos): array
{
    $equivalencias = [
        'geral' => ['geral', 'Atendimento Geral'],
        'atendimento geral' => ['geral', 'Atendimento Geral'],
        'isenção' => ['isenção', 'Atendimento de Isenção'],
        'atendimento de isenção' => ['isenção', 'Atendimento de Isenção'],
        'prioridade' => ['prioridade', 'Atendimento de Prioridade'],
        'atendimento de prioridade' => ['prioridade', 'Atendimento de Prioridade'],
    ];

    $resultado = [];
    foreach ($servicos as $servico) {
        $servico = trim($servico);
        $resultado = array_merge(
            $resultado,
            $equivalencias[strtolower($servico)] ?? [$servico]
        );
    }

    return array_values(array_unique($resultado));
}