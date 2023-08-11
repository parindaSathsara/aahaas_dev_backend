<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
    <!-- <script src="https://test-gateway.mastercard.com/static/checkout/checkout.min.js" data-error="errorCallback" data-cancel="cancelCallback"></script> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="https://cbcmpgs.gateway.mastercard.com/static/checkout/checkout.min.js" data-error="errorCallback" data-beforeRedirect="Checkout.saveFormFields" data-afterRedirect="Checkout.restoreFormFields">
    </script>

</head>

<style>
    .btn__ClickPay:hover {
        border-color: #12a33e;
        background-color: #fff;
        color: #12a33e;
    }

    .main__Row {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>

<body>
    <div class="container main__Row">
        <div class="row ">
            <div class="col-md-6" style="display: block;margin:10px auto;box-sizing: border-box;box-shadow:0px 0px 5px 2px rgba(0,0,0,.2);border-radius:5px;">
                <div class="">
                    <img class="d-block flex-center" style="display:block;margin:0px auto;" src="<?php echo asset("Logo_paygateway.jpg") ?>" width="600" />
                </div>

                <div class="info__Section" style="display: block;margin:40px auto;padding:0px 40px;">
                    <p style="font-weight:bold;font-size:14px">Payment Ref: {{ $dataset->order_id }}</p>
                    <p style="font-weight:bold;font-size:14px">Total Amount (LKR): {{ $dataset->amount }}</p>
                    <p style="font-weight:bold;font-size:14px">Payment Type: {{ $dataset->payment_type }}</p>
                    <p style="font-weight:bold;font-size:14px">Customer E-mail: {{ $dataset->cx_email }}</p>

                    <p style="color:darkgrey;font-size:12px;font-style:italic;">Please contact us for any clarification : info@aahaas.com</p>
                </div>

                <button type="submit" class="btn btn-success d-block btn-sm btn__ClickPay" style="display:block;margin:50px auto;width:100%;border-radius:0;" onclick="Checkout.showPaymentPage();">Click to Pay</button>

            </div>
        </div>
    </div>

    <script type="text/javascript">
        function errorCallback(error) {
            console.log(JSON.stringify(error));
        }

        var sessionId = '{{$session_id}}'
        var version = '{{$version}}'
        Checkout.configure({
            session: {
                id: sessionId,
                // version: version
            }
        });

        $('#exampleModal').on('shown.bs.modal', function(e) {
            Checkout.showEmbeddedPage('#hco-embedded')
        });
    </script>
</body>

</html>