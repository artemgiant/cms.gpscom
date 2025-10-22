<?php


namespace App\Services;


use App\Models\Client;

class ReportingService
{
    /**
     * Отримання силки на кспорт
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function getExcelExportLink()
    {
        $link = 'reporting-export?';

        if (request()->get('type')) {
            $link .= 'type=' . request()->get('type') . '&';
        } else {
            $link .= 'type=' . Client::REPORT_PER_MONTH . '&';
        }

        if (request()->get('client_type')) {
            $link .= 'client_type=' . request()->get('client_type') . '&';
        } else {
            $link .= 'client_type=' . Client::IP . '&';
        }

        if (request()->get('mount')) {
            $link .= 'mount=' . request()->get('mount');
        } else {
            $link .= 'mount=' . date('m.Y');
        }

        return url($link);
    }
}
