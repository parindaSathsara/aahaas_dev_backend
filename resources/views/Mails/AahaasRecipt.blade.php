 <!DOCTYPE html>
 <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet">  -->


 <div class="mainOuter__Box" style="width: 794px;height: 1000px;margin: 0px auto;box-sizing: border-box;box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, .2);">
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
                 <td style="text-align: right;">{{$hotelName?$hotelName:$ResHotelName}}</td>
             </tr>

             <tr>
                 <td style="text-align: left;">Check-out Time: {{$checkout_time}}</td>
                 <td style="text-align: center;">Child: {{$no_of_childs}}</td>
                 <td style="text-align: right;">{!!$hotelAddress!!}</td>
             </tr>

             <tr>
                 <td style="text-align: left;">Category: {{$room_type}}</td>
                 <td style="text-align: center;">CWB: 0</td>
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
                     <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Rate ({{$currency}})</th>
                     <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Total ({{$currency}})</th>
                 </tr>
             </thead>
             <tbody style="font-size: 14px;border-collapse: collapse;padding: 10px;">
                 <tr>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Adult</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$bed_type}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$no_of_adults}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$checkin_date}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{$otherData->adult_rate}}</td>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">{{number_format($otherData->adult_rate * $no_of_adults,2)}}</td>
                 </tr>
                 <tr style="font-size: 14px;border-collapse: collapse;padding: 10px;">
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;" colspan="6"></td>
                 </tr>

                 <tr style="font-size: 14px;border-collapse: collapse;padding: 10px;">
                     <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Child Related</th>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;" colspan="5"></td>
                 </tr>

                 <tr style="font-size: 14px;border-collapse: collapse;padding: 10px;">
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;" colspan="6"></td>
                 </tr>

                 <tr style="font-size: 14px;border-collapse: collapse;padding: 10px;">
                     <th style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;">Meal Related</th>
                     <td style="border: 1px solid #cecece;text-align: center;border-collapse: collapse;padding: 10px;" colspan="5"></td>
                 </tr>
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