<!DOCTYPE html>
<html lang="en">

<body>


    <div class="main__Box" style="width: 794px;height:auto;box-sizing:border-box;box-shadow: 0px 0px 2px 0px rgba(0, 0, 0, .1);margin:0px auto auto -50px;">

        <div style="margin: 20px 10px 10px 10px;">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding-left:30px; font-family:'Arial';font-size:12px;color:#7e7e7e;"><b>Sharmila Travels & Tours (pvt) Ltd</b></td>
                        <td style="padding-left: 280px;"><img src="https://i.ibb.co/Vw4fLG2/hotelbeds.png" width="200" alt="hotelbeds" /></td>
                    </tr>
                    <tr>
                        <td style="padding-left:30px; font-family:'Arial';font-size:12px;color: #909090;">F 23-26, PEOPLE'S Park</td>
                        <td style="padding-left: 280px;font-family:'Arial';font-size:12px;color:#7e7e7e;"><b>HOTELBEDS</b></td>
                    </tr>
                    <tr>
                        <td style="padding-left:30px; font-family:'Arial';font-size:12px;color: #909090;">Colombo</td>
                        <td style="padding-left: 280px;color: #909090;font-family:'Arial';font-size:12px;">100 Complejo Miral Balear,</td>
                    </tr>
                    <tr>
                        <td style="padding-left:30px; font-family:'Arial';font-size:12px;color: #909090;">Sri Lanka</td>
                        <td style="padding-left: 280px;color: #909090;font-family:'Arial';font-size:12px;">Cami son Fangos PALMA DE</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="padding-left: 280px;color: #909090;font-family:'Arial';font-size:12px;">MALLORCA, 07007</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 160px;font-family:'Arial';">
            <h2 style="text-align: center;font-family:'Arial';color:#454545;font-weight:400;">Voucher/Accommodation</h2>
            <p style="text-align: center;font-family:'Arial';line-height:.05rem;color:#818181;font-size:14px;">Booking confirmed and guaranteed - Voucher - Hotel</p>
        </div>

        <div style="margin: 10px;">
            <table style="width: 100%;border-collapse: collapse;font-family:'Arial';">
                <thead>
                    <tr>
                        <th></th>
                        <th colspan="2"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;background-color:#404040;padding:25px 20px 10px 20px; color:#fafafa;line-height:.6rem;font-weight: lighter; font-size:12px;">
                            Reference Number:
                        </td>
                        <td style="background-color: #525252;color:#fafafa;padding:25px 0 10px 40px;font-weight: bold;font-size:12px;">Passenger name:</td>
                        <td style="background-color: #525252;color:#fafafa;padding:25px 0 10px 40px;font-weight: bold;font-size:12px;">{{$resevation_name}}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;background-color:#404040;padding:10px; color:#fafafa;line-height:.6rem;font-size: 22px; font-weight:400;">{{$resevation_no}}</td>
                        <td style="background-color: #525252;color:#fafafa;padding:10px 0 10px 40px;font-weight: bold;font-size:12px;">Booking date:</td>
                        <td style="background-color: #525252;color:#fafafa;padding:10px 0 10px 40px;font-weight: bold;font-size:12px;">{{$resevation_date}}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;background-color:#404040;padding:10px 20px 20px 25px; color:#fafafa;line-height:.6rem;font-weight: lighter;font-size:12px;">Valid for the hotel</td>
                        <td style="background-color: #525252;color:#fafafa;padding:10px 0 25px 40px;font-weight: bold;font-size:12px;">File TO:</td>
                        <td style="background-color: #525252;color:#fafafa;padding:10px 0 25px 40px;font-weight: bold;font-size:12px;">-</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin:10px;font-family:'Arial';">
            <span style="font-size: 14px;color: #909090;">From: {{$checkin_date}} To: {{$checkout_time}}</span>

            <table style="width: 100%;text-align: left;border-collapse: collapse;margin-top:20px;margin-bottom:20px;font-family:'Arial';">
                <thead style="border-bottom: 1px solid #cecece;font-size: 12px;color: #595959;font-weight:600;text-align: center !important;">
                    <tr>
                        <th style="padding-bottom:5px;">Units</th>
                        <th style="padding-bottom:5px;">Room Type</th>
                        <th style="padding-bottom:5px;">Board</th>
                        <th style="padding-bottom:5px;">Occupancy</th>
                    </tr>
                </thead>
                <tbody style="color:#787878;font-size: 12px;text-align: center;">
                    @foreach ($otherdata as $main_data)
                    <tr>
                        <td style="padding-top:5px;">1</td>
                        <td style="padding-top:5px;">{{$main_data->room_type}}</td>
                        @if ($board_code == 'BB')
                        <td style="padding-top:5px;">Bed and Breakfast</td>
                        @elseif ($board_code == 'HB')
                        <td style="padding-top:5px;">Half Board</td>
                        @elseif ($board_code == 'FB')
                        <td style="padding-top:5px;">Full Board</td>
                        @elseif ($board_code == 'RO')
                        <td style="padding-top:5px;">Room Only</td>
                        @endif

                        <td style="padding-top:5px;">{{$main_data->no_of_adults}} adult(s), {{$main_data->no_of_childs}} Child(s)</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <hr>
            <div style="margin: 10px;">
                <p style="color: #595959;font-size:14px"><b>Remarks</b></p>
                <p style="text-align: justify;color:#8e8e8e;line-height:1.3rem;font-size: 13px;">Car park NO. Check-in hour 18:00 - 23:00. Due to the pandemic, many accommodation and service providers may
                    implement processes and policies to help protect the safety of all of us. This may result in the unavailability or changes
                    in certain services and amenities that are normally available from them. More info here https://cutt.ly/MT8BJcv
                    (18/05/2020-31/12/2022).</p>
            </div>
        </div>
    </div>
</body>

</html>