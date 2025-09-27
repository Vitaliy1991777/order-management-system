<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Таблица заказов') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- ВОТ ЭТА ФОРМА БЫЛА ПРОПУЩЕНА --}}
                    <form method="GET" action="{{ route('orders.index') }}" class="mb-4">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Поиск</label>
                                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="ФИО, компания, телефон, товар">
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">С даты</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">По дату</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Статус</label>
                                <select id="status" class="form-select" name="status">
                                    <option value="">Все</option>
                                    <option value="новый" @selected(request('status') == 'новый')>Новый</option>
                                    <option value="в работе" @selected(request('status') == 'в работе')>В работе</option>
                                    <option value="завершён" @selected(request('status') == 'завершён')>Завершён</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Применить</button>
                                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Сброс</a>
                            </div>
                        </div>
                    </form>

                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#statsModal">Статистика</button>
                    </div>

                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Дата</th>
                                <th>ФИО</th>
                                <th>Телефон</th>
                                <th>ИНН</th>
                                <th>Компания</th>
                                <th>Адрес</th>
                                <th>Товар</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td>{{ $order->created_at->format('d.m.Y') }}</td>
                                    <td>{{ $order->customer_full_name }}</td>
                                    <td>{{ $order->customer_phone }}</td>
                                    <td>{{ $order->customer_tin }}</td>
                                    <td>{{ $order->customer_company_name }}</td>
                                    <td>{{ $order->customer_address }}</td>
                                    <td>
                                        {{ $order->items->pluck('name')->implode(', ') }}
                                    </td>
                                    <td>{{ $order->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Заказов пока нет.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Модальное окно для статистики --}}
<div class="modal fade" id="statsModal" tabindex="-1" aria-labelledby="statsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="statsModalLabel">Статистика заказов</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(isset($stats))
                    <ul>
                        <li>Всего заказов (с учётом фильтров): <strong>{{ $stats['total'] }}</strong></li>
                        <li>Новых заказов: <strong>{{ $stats['new'] }}</strong></li>
                        <li>В работе: <strong>{{ $stats['in_progress'] }}</strong></li>
                        <li>Завершённые: <strong>{{ $stats['completed'] }}</strong></li>
                    </ul>
                @else
                    <p>Не удалось загрузить статистику.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>