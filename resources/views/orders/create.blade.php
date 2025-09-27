<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Создание заказа</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa; /* Светло-серый фон, как на макете */
        }
        .form-container {
            max-width: 650px; /* Ограничиваем ширину формы */
            margin: 50px auto; /* Центрируем по горизонтали и добавляем отступ сверху */
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="font-sans antialiased">

    <div class="container">
        <div class="form-container">
            <h4 class="mb-4 text-center">Создание заказа</h4>

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('orders.store') }}">
                @csrf

                {{-- Поля клиента --}}
                <div class="mb-3">
                    <label for="full_name" class="form-label">ФИО<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Телефон<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Почта</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>

                <div class="mb-3">
                    <label for="tin" class="form-label">ИНН</label>
                    <input type="text" class="form-control" id="tin" name="tin">
                </div>

                <div class="mb-3">
                    <label for="company_name" class="form-label">Название компании</label>
                    <input type="text" class="form-control" id="company_name" name="company_name">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Адрес</label>
                    <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                </div>

                <hr class="my-4">

                {{-- Товары --}}
                <div class="row mb-2">
                    <div class="col"><label class="form-label small">Товары</label></div>
                    <div class="col-2"><label class="form-label small">Кол-во</label></div>
                    <div class="col-3"><label class="form-label small">Единица измерения</label></div>
                    <div class="col-1"></div>
                </div>

                <div id="product-list">
                    <div class="row product-row mb-2 align-items-center">
                        <div class="col">
                            <input type="text" class="form-control" name="products[0][name]" placeholder="Наименование">
                        </div>
                        <div class="col-2">
                            <input type="number" class="form-control" name="products[0][quantity]" value="1">
                        </div>
                        <div class="col-3">
                            <select class="form-select" name="products[0][unit]">
                                <option value="штуки">штуки</option>
                                <option value="комплекты">комплекты</option>
                            </select>
                        </div>
                        <div class="col-1">
                            {{-- Пустое место для кнопки удаления в будущих строках --}}
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-dark btn-sm mt-2" id="add-product-btn">Добавить товар</button>

                <hr class="my-4">

                <div class="d-grid">
                    <button type="submit" class="btn btn-dark btn-lg">Создать заказ</button>
                </div>
            </form>
        </div>
    </div>


    {{-- Скрипт для добавления/удаления товаров --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addProductBtn = document.getElementById('add-product-btn');
            const productList = document.getElementById('product-list');
            let productIndex = 1;

            addProductBtn.addEventListener('click', function () {
                const newRow = document.createElement('div');
                newRow.className = 'row product-row mb-2 align-items-center';
                newRow.innerHTML = `
                    <div class="col">
                        <input type="text" class="form-control" name="products[${productIndex}][name]" placeholder="Наименование">
                    </div>
                    <div class="col-2">
                        <input type="number" class="form-control" name="products[${productIndex}][quantity]" value="1">
                    </div>
                    <div class="col-3">
                        <select class="form-select" name="products[${productIndex}][unit]">
                            <option value="штуки">штуки</option>
                            <option value="комплекты">комплекты</option>
                        </select>
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-danger btn-sm remove-product-btn">X</button>
                    </div>
                `;
                productList.appendChild(newRow);
                productIndex++;
            });

            productList.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('remove-product-btn')) {
                    e.target.closest('.product-row').remove();
                }
            });
        });
    </script>
</body>
</html>