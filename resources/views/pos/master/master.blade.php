<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/') }}public/pos/pos.css?v=1"/>
        <link rel="shortcut icon" href="{{ asset('/') }}{{ $icon }}">

   @yield('css')
</head>
<body>

    <div class="pos-container">
        @include('pos.include.header')

        <main class="pos-content">
            @yield('body')
        </main>
    </div>

    @include('pos.include.customerModal')
    @include('pos.include.calculatorModal')
    @include('pos.include.expiredProductModal')

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <script>
        const customerSearchUrl = "{{ route('customers.search') }}";
        const customerStoreUrl = "{{ route('customers.store') }}";

        document.addEventListener('DOMContentLoaded', function() {
            // --- Customer Search & Add Logic ---
            const customerSearchInput = document.getElementById('customer-search');
            const customerResultsContainer = document.getElementById('customer-results');
            const selectedCustomerIdInput = document.getElementById('selected-customer-id');
            const saveCustomerBtn = document.getElementById('saveCustomerBtn');
            const addCustomerModalEl = document.getElementById('addCustomerModal');
            const addCustomerModal = new bootstrap.Modal(addCustomerModalEl);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const selectCustomer = (customer) => {
                customerSearchInput.value = customer.name;
                selectedCustomerIdInput.value = customer.id;
                customerResultsContainer.style.display = 'none';
                customerResultsContainer.innerHTML = '';
            };
            
            const setDefaultCustomer = () => {
                customerSearchInput.value = '';
                selectedCustomerIdInput.value = '1'; 
            };
            setDefaultCustomer();


            customerSearchInput.addEventListener('keyup', async () => {
                const query = customerSearchInput.value;
                if (query.length < 1) { 
                    customerResultsContainer.style.display = 'none';
                    return;
                }

                try {
                    const response = await fetch(`${customerSearchUrl}?q=${query}`);
                    const customers = await response.json();
                    
                    customerResultsContainer.innerHTML = '';
                    if (customers.length > 0) {
                        customers.forEach(customer => {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.classList.add('list-group-item', 'list-group-item-action');
                            item.textContent = `${customer.name} - ${customer.phone || ''}`;
                            item.addEventListener('click', () => {
                                selectCustomer(customer);
                            });
                            customerResultsContainer.appendChild(item);
                        });
                        customerResultsContainer.style.display = 'block';
                    } else {
                        customerResultsContainer.style.display = 'none';
                    }
                } catch (error) {
                    console.error('Error fetching customers:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Search Failed',
                        text: 'Could not fetch customer data from the server.',
                    });
                }
            });

            document.addEventListener('click', (e) => {
                if (!customerSearchInput.parentElement.contains(e.target)) {
                    customerResultsContainer.style.display = 'none';
                }
            });

                 // --- SCRIPT UPDATE STARTS HERE ---
            saveCustomerBtn.addEventListener('click', async () => {
                const name = document.getElementById('customer-name-modal').value.trim();
                const phone = document.getElementById('customer-phone-modal').value.trim();
                const address = document.getElementById('customer-address-modal').value.trim();

                // 1. Client-side validation for all required fields
                let errors = [];
                if (name === '') errors.push('Customer name is required.');
                if (phone === '') errors.push('Phone number is required.');
                if (address === '') errors.push('Address is required.');

                if (errors.length > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        html: errors.join('<br>'),
                    });
                    return;
                }

                try {
                    const response = await fetch(customerStoreUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken 
                        },
                        body: JSON.stringify({ name, phone, address })
                    });
                    
                    // 2. Specific handling for server-side validation errors (e.g., unique phone)
                    if (response.status === 422) { // Unprocessable Entity
                        const errorData = await response.json();
                        const errorMessages = Object.values(errorData.errors).flat();
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Failed',
                            html: errorMessages.join('<br>'),
                        });
                        return; 
                    }

                    if (!response.ok) {
                        throw new Error(`Server responded with status: ${response.status}`);
                    }

                    const newCustomer = await response.json();
                    selectCustomer(newCustomer);
                    
                    document.getElementById('addCustomerForm').reset();
                    addCustomerModal.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Customer Added!',
                        text: `${newCustomer.name} has been successfully added and selected.`,
                        timer: 2000,
                        showConfirmButton: false
                    });

                } catch (error) {
                    console.error('Error saving customer:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Creation Failed',
                        text: 'An unexpected error occurred. Please try again.',
                    });
                }
            });
            // --- SCRIPT UPDATE ENDS HERE ---


            // --- Original Calculator Script ---
            const calculatorModal = document.getElementById('calculatorModal');
            if (!calculatorModal) return;
            const display = calculatorModal.querySelector('.calculator-display');
            const keys = calculatorModal.querySelector('.calculator-keys');
            const state = {
                displayValue: '0', firstValue: null, operator: null, waitingForSecondValue: false,
            };
            function updateDisplay() {
                let value = state.displayValue;
                if (value.length > 9) value = parseFloat(value).toExponential(5);
                display.textContent = value;
            }
            function resetCalculator() {
                state.displayValue = '0'; state.firstValue = null; state.operator = null; state.waitingForSecondValue = false;
            }
            function inputDigit(digit) {
                if (state.waitingForSecondValue) {
                    state.displayValue = digit; state.waitingForSecondValue = false;
                } else {
                    state.displayValue = state.displayValue === '0' ? digit : state.displayValue + digit;
                }
            }
            function inputDecimal() {
                if (state.waitingForSecondValue) {
                    state.displayValue = '0.'; state.waitingForSecondValue = false; return;
                }
                if (!state.displayValue.includes('.')) state.displayValue += '.';
            }
            function performCalculation(first, second, op) {
                if (op === 'add') return first + second;
                if (op === 'subtract') return first - second;
                if (op === 'multiply') return first * second;
                if (op === 'divide') {
                    if (second === 0) return 'Error';
                    return first / second;
                }
                return second;
            }
            function handleOperator(nextOperator) {
                const { firstValue, displayValue, operator } = state;
                const inputValue = parseFloat(displayValue);
                if (operator && state.waitingForSecondValue) {
                    state.operator = nextOperator; return;
                }
                if (firstValue == null && !isNaN(inputValue)) {
                    state.firstValue = inputValue;
                } else if (operator) {
                    const result = performCalculation(firstValue, inputValue, operator);
                    if (result === 'Error') {
                        state.displayValue = 'Error';
                        updateDisplay();
                        setTimeout(() => { resetCalculator(); updateDisplay(); }, 1500);
                        return;
                    }
                    state.displayValue = String(parseFloat(result.toFixed(7)));
                    state.firstValue = result;
                }
                state.waitingForSecondValue = true;
                state.operator = nextOperator;
            }
            keys.addEventListener('click', (e) => {
                const key = e.target;
                if (!key.matches('button')) return;
                const action = key.dataset.action;
                const keyContent = key.textContent;
                if (!action) inputDigit(keyContent);
                else if (action === 'decimal') inputDecimal();
                else if (action === 'toggle-sign') state.displayValue = String(parseFloat(state.displayValue) * -1);
                else if (action === 'percentage') state.displayValue = String(parseFloat(state.displayValue) / 100);
                else if (action === 'clear') resetCalculator();
                else if (['add', 'subtract', 'multiply', 'divide'].includes(action)) handleOperator(action);
                else if (action === 'calculate') {
                    const { firstValue, displayValue, operator } = state;
                    if (operator && !state.waitingForSecondValue) {
                        const secondValue = parseFloat(displayValue);
                        const result = performCalculation(firstValue, secondValue, operator);
                        if (result === 'Error') {
                            state.displayValue = 'Error';
                            setTimeout(() => { resetCalculator(); updateDisplay(); }, 1500);
                        } else {
                            state.displayValue = String(parseFloat(result.toFixed(7)));
                            state.firstValue = null;
                            state.operator = null;
                            state.waitingForSecondValue = true;
                        }
                    }
                }
                updateDisplay();
            });
            updateDisplay();
        });
    </script>
    @yield('script')
</body>
</html>