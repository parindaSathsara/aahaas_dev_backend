 <!DOCTYPE html>
 <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet">  -->


 <div class="mainOuter__Box" style="width: 700px;height: 1000px;margin: 0px auto;box-sizing: border-box;box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, .2);">
     <div class="secondOuter__Box">
         <div class="img_box">
             <img src="https://i.ibb.co/dBQHZWQ/aahaas.png" style="margin: 20px; text-align:left;" width="120px" alt="aahaas_logo" class="logo__aahaas" />
         </div>

         <table style="width: 94%; margin:20px;">
             <thead>
                 <tr>
                     <td><b>Invoice No:</b> #{{$invoice_id}}</td>
                     <td style="text-align: right;">No: 148,</td>
                 </tr>
                 <tr>
                     <td><b>Resevation No:</b> {{$resevation_no}}</td>
                     <td style="text-align: right;">Aluthmawatha Road,</td>
                 </tr>
                 <tr>
                     <td><b>Resevation Status:</b> {{$resevation_status}}</td>
                     <td style="text-align: right;">Colombo 15,</td>
                 </tr>
                 <tr>
                     <td><b>Created:</b> {{$resevation_date}}</td>
                     <td style="text-align: right;">+(94) 112 351 15</td>
                 </tr>
                 <tr>
                     <td><b>Customer Name:</b> {{$resevation_name}}</td>
                     <td style="text-align: right;">Email: info@appleholidaysds.com</td>
                 </tr>
             </thead>
         </table>
     </div>
     <hr style="width: 90%;">
     <table style="width: 94%; margin:20px 20px 20px 20px;">
         <thead>
             <tr>
                 <td style="text-align: left;"><u>Room Details</u></td>
                 <td style="text-align: center;"><u>Pax Details</u></td>
                 <td style="text-align: right;"><u>Hotel Details</u></td>
             </tr>

             <tr>
                 <td style="text-align: left;">Check-in Time: {{$checkin_date}}</td>
                 <td style="text-align: center;">Adult: {{$no_of_adults}}</td>
                 <td style="text-align: right;">{{$hotelName}}</td>
             </tr>

             <tr>
                 <td style="text-align: left;">Check-out Time: {{$checkout_time}}</td>
                 <td style="text-align: center;">Child: {{$no_of_childs}}</td>
                 <td style="text-align: right;">{!!$hotelAddress!!}</td>
             </tr>

             <tr>
                 <td style="text-align: left;">Category: {{$room_type}}</td>
                 <td style="text-align: center;">CWB: 0</td>
                 <td style="text-align: right;">{{$hotelEmail}}</td>
             </tr>

             <tr>
                 @if ($board_code == 'BB')
                 <td style="text-align: left;">Meal: Bed & Breakfast</td>
                 @elseif ($board_code == 'HB')
                 <td style="text-align: left;">Meal: Half Board</td>
                 @elseif ($board_code == 'FB')
                 <td style="text-align: left;">Meal: Full Board</td>
                 @elseif ($board_code == 'RO')
                 <td style="text-align: left;">Meal: Room Only</td>
                 @endif
                 <td style="text-align: center;">CNB: 0</td>
             </tr>

             <tr>
                 <td style="text-align: left;">Night(s): {{$nights}}</td>
             </tr>
         </thead>
     </table>

     <div class="middle__Table" style="margin: 20px;">
         <table style="border: 1px solid #cecece;width: 100%;text-align: center;border-collapse: collapse;">
             <thead style="text-align: center;border: 1px solid #cecece;border-collapse: collapse;padding: 10px;">
                 <tr style="border-collapse: collapse;padding: 10px;">
                     <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Pax Type</th>
                     <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Room Type</th>
                     <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Room/Pax Count</th>
                     <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Date</th>
                     <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Rate ($)</th>
                     <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Total ($)</th>
                 </tr>
             </thead>
             <tbody style="font-size: 14px;border-collapse: collapse;padding: 10px;">
                 @foreach ($otherData as $main_data)
                 <tr>
                     @if ($main_data->PaxType == 'AD')
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Adult</td>
                     @else
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Child</td>
                     @endif
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$bed_type}}</td>
                     @if ($main_data->PaxType == 'AD')
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$main_data->AdultCount}}</td>
                     @else
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$main_data->ChildCount}}</td>
                     @endif
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$checkin_date}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">-</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">-</td>
                 </tr>
                 @endforeach

                 <tr style="font-size: 14px;border-collapse: collapse;padding: 10px;">
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;" colspan="6"></td>
                 </tr>

                 <tr style="font-size: 14px;border-collapse: collapse;padding: 10px;">
                     <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Child Related</th>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;" colspan="5"></td>
                 </tr>

                 @foreach ($otherData as $otherdata)
                 @if ($otherdata->PaxType == 'CH')
                 <tr style="font-size: 14px;border-collapse: collapse;padding: 10px;">
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$otherdata->SerType}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">-</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$otherdata->SerChildCount}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$otherdata->SerDate}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">${{$otherdata->SerPerPrice}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">${{$otherdata->SerPerPrice}}</td>
                 </tr>
                 @endif
                 @endforeach

                 <tr style="font-size: 14px;border-collapse: collapse;padding: 10px;">
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;" colspan="6"></td>
                 </tr>

                 <tr style="font-size: 14px;border-collapse: collapse;padding: 10px;">
                     <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Meal Related</th>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;" colspan="5"></td>
                 </tr>

                 @foreach ($meal_data as $mealdata)
                 @if ($mealdata->adult_count >= '1')
                 <tr style="font-size: 14px;border-collapse: collapse;padding: 10px;">
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$mealdata->meal_plan}} Adult</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">-</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$mealdata->adult_count}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$mealdata->date}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">${{$mealdata->unit_price}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">${{$mealdata->unit_price}}</td>
                 </tr>
                 @elseif($mealdata->child_count >= '1')
                 <tr style="font-size: 14px;border-collapse: collapse;padding: 10px;">
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$mealdata->meal_plan}} Child</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">-</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$mealdata->child_count}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$mealdata->date}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">${{$mealdata->unit_price}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">${{$mealdata->unit_price}}</td>
                 </tr>
                 @endif
                 @endforeach
             </tbody>
             <tfoot>
                 <tr style="font-size: 14px;border-collapse: collapse;padding: 10px;">
                     <td style="border: 1px solid #cecece;text-align: left;border-collapse: collapse;padding: 10px;" colspan="5" style="text-align: left;">Net Total($)</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$total_amount}}</td>
                 </tr>
             </tfoot>
         </table>
     </div>



     <div class="footer__Box" style="margin: 40px 20px 20px 20px;">
         <span>Remarks: {{$special_notice}}</span>
         <p class="disclaimer__Para" style="font-size: 12px;color: dimgray;text-align: justify;">
             ***Please submit Proforma invoice for this reservation to settle the outstanding as it allows us the needed time
             to process your invoice promptly. If invoices do not have the proper information or invoices are not sent on time,
             it will delay the processing of your invoice. Furthermore, we may ask you to resubmit the invoice with the proper
             information. ***</p>
     </div>
 </div>