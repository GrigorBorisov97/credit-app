<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Credit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>

<body class="antialiased">
    <div class="container">
        <div class="row">
            <div class="col pt-4">
                <h2>New Credit</h2>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">@</span>
                    <input id="userName" type="text" class="form-control" placeholder="Username" aria-label="Username"
                        aria-describedby="basic-addon1">
                </div>

                <div class="input-group mb-3">
                    <input onkeyup="calcMonthlyPayment(event.target.value)" type="number" value="10000" max="80000" class="form-control" id="creditAmount"
                        aria-label="Amount (to the nearest dollar)">
                    <span class="input-group-text">.00 BGN</span>
                </div>

                <label for="customRange1" class="form-label">Return period - in <span id="returnPeriod">60</span>
                    months</label>
                <input type="range" min="3" max="120" default="60" class="form-range" id="returnPeriodRange">

                <p class="h4">Monthly payment - <span id="monthlyPayment"></span></p>
                <p class="h4">Total amount - <span id="totalAmount"></span></p>

                <button type="button" onclick="takeCredit()" class="btn btn-primary">Create credit</button>
            </div>
        </div>

        <div class="col pt-4">
            <h2>Credit listing</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Credit Amount</th>
                        <th scope="col">Remaining Amount</th>
                        <th scope="col">Return period</th>
                        <th scope="col">Monthly Tax</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($credits as $credit): ?>
                        <tr>
                            <th scope="row"><?= sprintf('%06s', $credit['id']) ?></th>
                            <td><?= $credit['name'] ?></td>
                            <td><?= $credit['credit_amount'] ?> BGN</td>
                            <td><?= $credit['refund_amount'] ?> BGN</td>
                            <td><?= $credit['return_period'] ?> months</td>
                            <td><?= $credit['monthly_tax'] ?> BGN</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="col pt-4">
                <h2>Charge credit</h2>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">@</span>
                    <select id="userPayment" onchange="updatePaymentField(event.target.value)" class="form-select" aria-label="Default select example">
                        <?php foreach($credits as $credit): ?>
                            <option value="<?= $credit['id'] ?>"><?= $credit['name'] ?> (<?= $credit['refund_amount'] ?> BGN)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-group mb-3">
                    <span class="input-group-text">Payment amount</span>
                    <input type="number" value="<?= $credits[0]['monthly_tax'] ?>" class="form-control" id="paymentAmount"
                        aria-label="Amount (to the nearest dollar)">
                </div>

                <button type="button" onclick="creditPayment()" class="btn btn-primary">Credit Payment</button>
            </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
        crossorigin="anonymous"></script>

    <script>
        let userName = '',
            returnPeriod,
            creditAmount,
            totalAmount,
            monthlyTax,
            monthlyPayment;

        let credits = JSON.parse(`<?= json_encode($credits) ?>`);

        function updatePaymentField(id) {
            document.querySelector('#paymentAmount').value = credits.find(el => el.id == id).monthly_tax;
        }

        function calcMonthlyPayment(input_value = false) {
            returnPeriod = parseInt(document.querySelector('#returnPeriodRange').value);
            if (input_value) {
                creditAmount = parseInt(input_value);
            } else {
                creditAmount = parseInt(document.querySelector('#creditAmount').value);
            }

            totalAmount = parseInt(document.querySelector('#creditAmount').value);

            monthlyTax = creditAmount / 100 * 7.9 / 12;
            totalAmount = creditAmount + (monthlyTax * returnPeriod);
            monthlyPayment = totalAmount / returnPeriod;

            document.querySelector('#monthlyPayment').innerHTML = monthlyPayment.toFixed(2) + ' BGN';
            document.querySelector('#totalAmount').innerHTML = totalAmount.toFixed(2) + ' BGN';
        };
        calcMonthlyPayment();

        document.querySelector('#returnPeriodRange').addEventListener('change', (event) => {
            document.querySelector('#returnPeriod').innerHTML = event.target.value;
            calcMonthlyPayment();
        })

        document.querySelector('#userName').addEventListener('change', (event) => {
            userName = event.target.value;
        })


        async function takeCredit() {
            const ajax = await fetch('./api/credit-new', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    returnPeriod,
                    creditAmount,
                    totalAmount,
                    monthlyPayment,
                    userName
                })
            })

            const response = await ajax.json();

            alert(response.message);
            if (response.status === 'success') {
                window.location.reload();
            }
        }

        async function creditPayment() {
            let userPayment = document.querySelector('#userPayment').value;
            let paymentAmount = document.querySelector('#paymentAmount').value;

            const ajax = await fetch('./api/credit-pay', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    userPayment,
                    paymentAmount
                })
            });
            const response = await ajax.json();

            alert(response.message);
            if (response.status === 'success') {
                window.location.reload();
            }
        }
    </script>
</body>

</html>