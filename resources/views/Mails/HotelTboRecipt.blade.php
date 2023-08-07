<!DOCTYPE html>

<body>
    <div class="main__Box" style="width: 700px;height: auto;margin: 0px auto;box-sizing: border-box;box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, .2);">
        <div class="top__UpperBox" style="padding: 20px;font-size: 18px;">
            <p style="padding: 10px;margin-bottom:-10px">Dear {{$resevation_name}},</p>
            <p style="padding: 10px; text-align: justify;line-height:28px;">
                Please note your request for <b>Confirmed</b> of booking with following details
                has been sent successfully to our operations team. They will process your request shortly.
            </p>
        </div>

        <div class="second__UpperBox" style="margin-left:30px;font-size: 18px;">
            <span style="display: block;text-align: left;line-height: 1.5rem;"><b>Booked By:</b> {{$resevation_name}}</span>
            <span style="display: block;text-align: left;line-height: 1.5rem;"><b>TBOH Confirmation Number:</b> {{$resevation_no}}</span>
            <span style="display: block;text-align: left;line-height: 1.5rem;"><b>Booking Amount:</b> {{$total_amount}}</span>
            <span style="display: block;text-align: left;line-height: 1.5rem;"><b>Check In:</b> {{$checkin_date}}</span>
            <span style="display: block;text-align: left;line-height: 1.5rem;"><b>Check Out:</b> {{$checkout_time}}</span>
            <span style="display: block;text-align: left;line-height: 1.5rem;"><b>Nationality:</b> -</span>
            <span style="display: block;text-align: left;line-height: 1.5rem;"><b>HotelName:</b> {{$hotelName}}</span>
        </div>

        <div style="margin: 30px;">
            <span style="color: red;"><b>Last Cancellation Date: {{$cancel_dealine}}</b></span>
        </div>

        <div class="center__TableBox" style="margin: 20px;">
            <table style="border: 1px solid #cecece;width: 100%;text-align: center;border-collapse: collapse;">
                <thead style="text-align: center;border: 1px solid #cecece;border-collapse: collapse;padding: 10px;">
                    <tr style="border-collapse: collapse;padding: 10px;">
                        <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 5px;background-color:#003063; color:#fff;">Room Name</th>
                        <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 5px;background-color:#003063; color:#fff;">Lead Guest Name </th>
                        <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 5px;background-color:#003063; color:#fff;">No. of Adults</th>
                        <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 5px;background-color:#003063; color:#fff;">No. of Children(Age In Years)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($otherdata as $data)
                    <tr>
                        <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$data->room_code}}
                            Incl: {{$board_code}}
                        </td>
                        <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$data->first_name }} {{$data->last_name}}
                        </td>
                        <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$data->adult_count}}</td>
                        <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$data->child_count}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="lower__TableBox" style="margin: 20px;">
            <table style="border: 1px solid #cecece;width: 100%;text-align: center;border-collapse: collapse;">
                <thead style="text-align: center;border: 1px solid #cecece;padding: 10px;">
                    <tr style=" text-align: left;border-collapse: collapse;padding: 10px;">
                        <th colspan="3" style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 5px;">Cancellation Policy</th>
                    </tr>
                    <tr style="border-collapse: collapse;padding: 10px;">
                        <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 5px; background-color:gainsboro;">Cancelled on or Before</th>
                        <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 5px; background-color:gainsboro;">Cancellation Charge</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 5px;">{{$cancel_dealine}}</td>
                        <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 5px;">{{$cancellationAmount}}</td>
                    </tr>
                    <tr style="text-align:left;">
                        <td colspan="3" style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 5px;">No Show will attract full cancellation charge unless otherwise specified</td>
                    </tr>
                </tbody>
                <tfoot style="text-align:left;">
                    <tr style="text-align:left;">
                        <td colspan="3" style="font-size:12px; color:#003063;border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 5px;">Early check out will attract full cancellation charge unless otherwise specified</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <p style="margin: 20px;text-align:justify;">In case of any operational issue you can drop mail at <a href="#">ops@tboholidays.com</a> or call +9714-4357520</p>
        <p style="margin: 20px;text-align:justify;line-height:.8rem;">Regards</p>
        <p style="margin: 20px;text-align:justify;line-height:1px;">Team Aahaas</p>
    </div>
</body>