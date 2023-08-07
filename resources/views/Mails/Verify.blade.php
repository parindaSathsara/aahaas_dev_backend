<!DOCTYPE html>
<html lang="en">

<!-- <style>
    hr:after {
        background: #fff;
        content: '';
        position: relative;
        top: -13px;
    }
</style> -->

<body>

    <div style="width: 500px;height: 850px;margin: 0px auto;box-sizing: border-box;box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, .2);">
        <img src="https://i.ibb.co/dBQHZWQ/aahaas.png" style="margin: 20px;margin-left:38%;margin-right:auto;" width="120px" alt="aahaas_logo" class="logo__aahaas" />
        <div class="secondBox" style="background-color:#039; padding:15px">

            <img src="https://i.ibb.co/1QzmyVb/mail-300.png" alt="mail-300" style="width: 50px;margin-left:45%;margin-right:auto;margin-top:15px">

            <p style="margin-top:5px;letter-spacing:2px;text-align: center;color:#fff;font-family:'Calibri', sans-serif;font-size:16px;">THANKS FOR SIGNING UP!</p>
            <p style="margin-top:5px;text-align: center;color:#fff;font-family:'Calibri';font-size:30px;">Verify Your E-mail Address</p>
        </div>

        <div class="middleBox" style="padding: 40px;">
            <p style="text-align:center;margin:10px;line-height:1.7rem;font-family:'Calibri';font-size:18px;font-weight:light;">
                <b>Hello {{ $user->username }}</b>, <br>You're almost ready to get started. Please click on the button below to verify your email address and enjoy exclusive services with us!
            </p>
        </div>

        <div style="text-align:center;padding: 10px;">
            <button style="background-color: #ff6600;border:none;padding:15px;color:#fff;border-radius:2px;cursor:pointer;width:200px;">
                <a href="{{ URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), ['id' => $user->id]) }}" class="btn_Verify" style="text-decoration:none;text-align: center;color:#fff;font-weight:bold;font-family:'Calibri';font-size:16px;">
                    VERIFY YOUR EMAIL
                </a>
            </button>
        </div>

        <div>
            <p style="text-align: center;line-height:1.7rem;font-family:'Calibri';font-size:18px;">Thanks, <br>Team Aahaas</p>
        </div>

        <div style="background-color: #e5eaf5;margin-top:40px;padding:10px;">
            <p style="text-align: center;line-height:1.5rem;font-family:'Calibri';"><b style="color: #003399;">Get in touch</b> <br>+(94) 112 351 15<br>info@appleholidaysds.com</p>
        </div>

        <div style="text-align: center;font-family:'Calibri';background-color:#039;color:#fff;padding:0.5px;">
            <p>Copyright &copy; Aahaas 2022, All Rights Reserved</p>
        </div>

    </div>
</body>

</html>