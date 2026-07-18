<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Event Processor</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

    <style>
        body{
            background:#f4f7fb;
        }

        .navbar{
            background:#0d6efd;
        }

        .navbar-brand{
            color:#fff!important;
            font-weight:bold;
            font-size:24px;
        }

        .card{
            border:none;
            border-radius:18px;
            box-shadow:0 8px 20px rgba(0,0,0,.08);
        }

        .card-header{
            background:#fff;
            border-bottom:1px solid #eee;
            font-weight:600;
            font-size:18px;
        }

        .btn-primary{
            border-radius:10px;
        }

        .btn-success{
            border-radius:10px;
        }

        .balance-card{
            background:linear-gradient(135deg,#0d6efd,#4b8dff);
            color:#fff;
        }

        .balance{
            font-size:40px;
            font-weight:bold;
        }

        .table th{
            background:#0d6efd;
            color:#fff;
        }

        footer{
            margin-top:40px;
            color:#777;
            text-align:center;
        }
    </style>

</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fa-solid fa-wallet"></i>
            Account Event Processor
        </a>
    </div>
</nav>

<div class="container mt-5">

    <div class="row g-4">

        <!-- LEFT -->

        <div class="col-lg-4">

            <div class="card">

                <div class="card-header">
                    <i class="fa-solid fa-plus"></i>
                    Submit Event
                </div>

                <div class="card-body">

                    <div id="message"></div>

                    <form id="eventForm">

                        <div class="mb-3">

                            <label class="form-label">
                                Event ID
                            </label>

                            <input
                                    type="text"
                                    id="event_id"
                                    class="form-control"
                                    placeholder="evt_001">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Account ID
                            </label>

                            <input
                                    type="text"
                                    id="account_id"
                                    class="form-control"
                                    placeholder="account_001">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Amount
                            </label>

                            <input
                                    type="number"
                                    id="amount"
                                    class="form-control"
                                    placeholder="500">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Occurred At
                            </label>

                            <input
                                    type="datetime-local"
                                    id="occurred_at"
                                    class="form-control">

                        </div>

                        <button
                                class="btn btn-primary w-100"
                                type="submit">

                            <i class="fa-solid fa-paper-plane"></i>

                            Submit Event

                        </button>

                    </form>

                </div>

            </div>

        </div>


        <!-- RIGHT -->

        <div class="col-lg-8">

            <!-- SEARCH -->

            <div class="card mb-4">

                <div class="card-header">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    Search Account

                </div>

                <div class="card-body">

                    <div class="input-group">

                        <input
                                type="text"
                                id="searchAccount"
                                class="form-control"
                                placeholder="Enter Account ID">

                        <button
                                class="btn btn-success"
                                id="searchBtn">

                            Search

                        </button>

                    </div>

                </div>

            </div>


            <!-- BALANCE -->

            <div class="card balance-card mb-4">

                <div class="card-body text-center">

                    <h5>Current Balance</h5>

                    <div
                            id="balance"
                            class="balance">

                        ₹0.00

                    </div>

                </div>

            </div>


            <!-- EVENTS -->

            <div class="card">

                <div class="card-header">

                    <i class="fa-solid fa-clock-rotate-left"></i>

                    Event History

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered align-middle">

                            <thead>

                            <tr>

                                <th>Event ID</th>

                                <th>Amount</th>

                                <th>Date</th>

                            </tr>

                            </thead>

                            <tbody id="eventTable">

                            <tr>

                                <td colspan="3" class="text-center text-muted">

                                    No Events Found

                                </td>

                            </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<footer>

    © 2026 Account Event Processor | Laravel Assignment

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('eventForm').addEventListener('submit', async function (e) {

    e.preventDefault();

    let message = document.getElementById('message');

    message.innerHTML = `
        <div class="alert alert-info">
            Processing...
        </div>
    `;

    const data = {
        id: document.getElementById('event_id').value,
        account_id: document.getElementById('account_id').value,
        amount: document.getElementById('amount').value,
        occurred_at: document.getElementById('occurred_at').value
    };

    try {

        const response = await fetch('/api/events', {

            method: 'POST',

            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },

            body: JSON.stringify(data)

        });

        const result = await response.json();

        if (response.status === 201) {

            message.innerHTML = `
                <div class="alert alert-success">
                    ${result.message}
                </div>
            `;

            document.getElementById('eventForm').reset();

        }
        else if (response.status === 200) {

            message.innerHTML = `
                <div class="alert alert-warning">
                    ${result.message}
                </div>
            `;

        }
        else if (response.status === 422) {

            let errors = "";

            Object.values(result.errors).forEach(function(item){

                errors += `<li>${item}</li>`;

            });

            message.innerHTML = `
                <div class="alert alert-danger">
                    <ul class="mb-0">${errors}</ul>
                </div>
            `;

        }
        else {

            message.innerHTML = `
                <div class="alert alert-danger">
                    Something went wrong.
                </div>
            `;

        }

    } catch (error) {

        message.innerHTML = `
            <div class="alert alert-danger">
                Server Error.
            </div>
        `;

    }

});
document.getElementById('searchBtn').addEventListener('click', async function () {

    let accountId = document.getElementById('searchAccount').value.trim();

    if(accountId == ""){

        alert("Please enter Account ID");

        return;

    }

    try{

        // Balance API

        const balanceResponse = await fetch('/api/accounts/' + accountId + '/balance');

        const balanceResult = await balanceResponse.json();

        if(balanceResponse.status == 404){

            document.getElementById('balance').innerHTML="₹0.00";

            document.getElementById('eventTable').innerHTML=`
                <tr>
                    <td colspan="3" class="text-center text-danger">
                        Account Not Found
                    </td>
                </tr>
            `;

            return;

        }

        document.getElementById('balance').innerHTML =
            "₹" + parseFloat(balanceResult.balance).toFixed(2);


        // Event History API

        const eventResponse = await fetch('/api/accounts/' + accountId + '/events');

        const eventResult = await eventResponse.json();

        let rows = "";

        if(eventResult.events.length == 0){

            rows = `
                <tr>
                    <td colspan="3" class="text-center">
                        No Events Found
                    </td>
                </tr>
            `;

        }else{

            eventResult.events.forEach(function(event){

                rows += `
                    <tr>

                        <td>${event.event_id}</td>

                        <td>${event.amount}</td>

                        <td>${new Date(event.occurred_at).toLocaleString()}</td>

                    </tr>
                `;

            });

        }

        document.getElementById('eventTable').innerHTML = rows;

    }

    catch(error){

        console.log(error);

        alert("Server Error");

    }

});
</script>
</body>
</html>