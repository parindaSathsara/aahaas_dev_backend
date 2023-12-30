 <!DOCTYPE html>
 <html lang="en">

 <body>
     <div style="width: 740px;height: auto;margin: 0 10px 0 -20px;box-sizing: border-box;box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, .2);">
         <div style="margin-bottom: 30px;">
             <p>Dear Sir/Madam,</p>
             <p>Greeting from Aahaas!</p>

             <p style="text-align: justify;line-height:1.4rem;">Thank you for your order. Please note that your order is confirmed. Please find the below details in your order. We looking forward to deliver the order within given timeline. If you have any inquires regarding your order
                 please use "My Orders" section to raise inquies or send an email to the below mail.<br>
                 <a style="color: #0084ff;text-decoration:underline;">info@aahaas.com</a>
             </p>
             <br />
             <br />

             <p><b style="color:#6f6f6f;margin-bottom:5px;">Order NO: </b><span style="color:#6f6f6f;">#{{$orderid}}</span></p>
             <p><b style="color:#6f6f6f;margin-bottom:5px;">Order Date: </b><span style="color:#6f6f6f;">{{$orderDate->format('Y-m-d')}}</span></p>
             <p><b style="color:#6f6f6f">Payment Type: </b><span style="color:#6f6f6f;">{{$payType}}</span></p>
         </div>

         @if (in_array('Essential', $categories) || in_array('Non Essential', $categories))
         <div>
             <p style="font-weight: 600;">Essential/Non Essential</p>

             <table style="border: 1px solid #cecece;width: 100%;text-align: center;border-collapse: collapse;font-size:14px;">
                 <thead style="text-align: center;border: 1px solid #cecece;border-collapse: collapse;padding: 10px;width:100%">
                     <tr>
                         <!-- <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Product ID</th> -->
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Product Title</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Product Desc</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Pref Delivery Date</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Delivery Address</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Qty</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Unit Price</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Net Price</th>
                     </tr>
                 </thead>
                 <tbody>

                     @foreach ($essData as $essenData)
                     <tr>
                         <!-- <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($essenData->MainCat == 'Essential' || $essenData->MainCat == 'Non Essential'){{$essenData->EssId}}@endif</td> -->
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($essenData->MainCat == 'Essential' || $essenData->MainCat == 'Non Essential'){{$essenData->EssNonTitle}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($essenData->MainCat == 'Essential' || $essenData->MainCat == 'Non Essential'){{$essenData->SKU}}{{$essenData->SKUUNIT}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($essenData->MainCat == 'Essential' || $essenData->MainCat == 'Non Essential'){{$essenData->DeliveryDate}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($essenData->MainCat == 'Essential' || $essenData->MainCat == 'Non Essential'){{$essenData->DeliveryAddress}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($essenData->MainCat == 'Essential' || $essenData->MainCat == 'Non Essential'){{$essenData->EssQuantity}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($essenData->MainCat == 'Essential' || $essenData->MainCat == 'Non Essential'){{$essenData->Currency}} {{$essenData->EachPrice}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($essenData->MainCat == 'Essential' || $essenData->MainCat == 'Non Essential'){{$essenData->Currency}} {{$essenData->EachPrice * $essenData->EssQuantity}}@endif</td>
                     </tr>
                     @endforeach

                 </tbody>
             </table>
         </div>
         @endif

         @if (in_array('Education', $categories))
         <div>
             <p style="font-weight: 600;">Education</p>

             <table style="border: 1px solid #cecece;width: 100%;text-align: center;border-collapse: collapse;font-size:13px;border:1px solid #e2e2e2">
                 <thead style="text-align: center;border: 1px solid #cecece;border-collapse: collapse;padding: 10px;width:100%;">
                     <tr>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Product ID</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Product Title</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Event Date</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Start Time</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">End Time</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Student Type</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Adult Rate</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Child Rate</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Total Price</th>
                     </tr>
                 </thead>
                 <tbody>

                     @foreach ($eduData as $educData)
                     <tr>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($educData->MainCat == 'Education'){{$educData->EduId}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($educData->MainCat == 'Education'){{$educData->course_name}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($educData->MainCat == 'Education'){{$educData->EduStartDate}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($educData->MainCat == 'Education'){{$educData->EduStartTime}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($educData->MainCat == 'Education'){{$educData->EduEndTime}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($educData->MainCat == 'Education' && $educData->StudentType == 'Children'){{'Child'}}@else{{ 'Adult' }}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($educData->MainCat == 'Education' && $educData->StudentType == 'Adult'){{$educData->Currency}} {{$educData->AdultStuFee}}@else{{'N/A'}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($educData->MainCat == 'Education' && $educData->StudentType == 'Children'){{$educData->Currency}} {{$educData->ChildStuFee}}@else{{'N/A'}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($educData->MainCat == 'Education' && $educData->StudentType == 'Children'){{$educData->Currency}} {{$educData->ChildStuFee}}@else{{$educData->AdultStuFee}}@endif</td>
                     </tr>
                     @endforeach

                 </tbody>
             </table>
         </div>
         @endif

         @if (in_array('Lifestyle', $categories))
         <div>
             <p style="font-weight: 600;">Life Style</p>

             <table style="border: 1px solid #cecece;width: 100%;text-align: center;border-collapse: collapse;font-size:13px;border:1px solid #e2e2e2">
                 <thead style="text-align: center;border: 1px solid #cecece;border-collapse: collapse;padding: 10px;width:100%;">
                     <tr>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Product ID</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Product Title</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Event Date</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Start Time</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">End Time</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Adult Rate</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Child Rate</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Total Price</th>
                     </tr>
                 </thead>
                 <tbody>
                     @foreach ($lsData as $lifeSData)
                     <tr>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($lifeSData->MainCat == 'Lifestyle'){{$lifeSData->LsId}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($lifeSData->MainCat == 'Lifestyle'){{$lifeSData->LSName}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($lifeSData->MainCat == 'Lifestyle'){{$lifeSData->LSBookDate}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($lifeSData->MainCat == 'Lifestyle'){{strstr($lifeSData->LSStartEndTime,'-',true)}}@else{{'N/A'}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($lifeSData->MainCat == 'Lifestyle'){{substr($lifeSData->LSStartEndTime,6)}}@else{{'N/A'}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($lifeSData->MainCat == 'Lifestyle')Count: {{$lifeSData->LSAdultCount}} Rate: {{$lifeSData->LSAdultRate}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($lifeSData->MainCat == 'Lifestyle')Count: {{$lifeSData->LSChildCount}} Rate: {{$lifeSData->LSChildRate}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($lifeSData->MainCat == 'Lifestyle'){{$lifeSData->TotPrice}}@endif</td>

                     </tr>
                     @endforeach
                 </tbody>
             </table>
         </div>
         @endif

         @if (in_array('Hotels', $categories))
         <div>
             <p style="font-weight: 600;">Hotel</p>

             <table style="border: 1px solid #cecece;width: 100%;text-align: center;border-collapse: collapse;font-size:13px;border:1px solid #e2e2e2">
                 <thead style="text-align: center;border: 1px solid #cecece;border-collapse: collapse;padding: 10px;width:100%;">
                     <tr>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Product ID</th>

                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Hotel Title</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Res. Date</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Check-in</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Check-out</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Night(s)</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Room Type</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Room Rate</th>
                         <th style="background-color:#525252;color:#fff;padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">Total Rate</th>
                     </tr>
                 </thead>
                 <tbody>
                     @foreach ($hotelData as $hotelRData)
                     <tr>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($hotelRData->MainCat == 'Hotels'){{$hotelRData->HotelId}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($hotelRData->MainCat == 'Hotels'){{$hotelRData->HotelName}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($hotelRData->MainCat == 'Hotels'){{$hotelRData->HotelBookDate}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($hotelRData->MainCat == 'Hotels'){{$hotelRData->CheckInHotel}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($hotelRData->MainCat == 'Hotels'){{$hotelRData->CheckOutHotel}}@endif</td>

                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">
                             @if($hotelRData->MainCat == 'Hotels')
                             {{ date_diff(new \DateTime($hotelRData->CheckInHotel), new \DateTime($hotelRData->CheckOutHotel))->format("%d Night(s)"); }}
                             @endif
                         </td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($hotelRData->MainCat == 'Hotels'){{$hotelRData->HotelRoomType}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($hotelRData->MainCat == 'Hotels'){{$hotelRData->TotPrice}}@endif</td>
                         <td style="padding:5px;border: 1px solid #cecece;text-align: center;border-collapse: collapse;">@if($hotelRData->MainCat == 'Hotels'){{$hotelRData->TotPrice}}@endif</td>
                     </tr>
                     @endforeach
                 </tbody>
             </table>
         </div>
         @endif

         <table style="font-size:14px;float:right;margin-top:10px">
             <tbody>
                 <tr>
                     <td style="text-align:left;">Sub Total</td>
                     <td style="text-align:right;">:{!! "&nbsp;" !!} {{ $currency_ }} {{ number_format(($total_amount - $deli_charge),2) }} </td>
                 </tr>
                 <tr>
                     <td style="text-align:left;">Delivery Charge</td>
                     <td style="text-align:right;">:{!! "&nbsp;" !!} {{ $currency_ }} {{ number_format($deli_charge,2) }}</td>
                 </tr>
                 <tr>
                     <td style="text-align:left;">Paid Amount</td>
                     <td style="text-align:right;">:{!! "&nbsp;" !!} {{ $currency_ }} {{ number_format($paid_amount,2) }}</td>
                 </tr>
                 <tr>
                     <td style="text-align:left;">To be paid on Delivery</td>
                     <td style="text-align:right;">:{!! "&nbsp;" !!} {{ $currency_ }} {{ number_format($bal_amount,2) }}</td>
                 </tr>
                 <tr>
                     <td style="text-align:left;">Net Total</td>
                     <td style="text-align:right;">:{!! "&nbsp;" !!} {{ $currency_ }} {{ number_format($total_amount,2) }}</td>
                 </tr>
             </tbody>
         </table>



         <div style="margin-top: 140px;">
             <hr />
             <span style="display:block;text-align:right;font-size:12px;">Customer Contact: <i>@if($user_contact != null || $user_contact != ''){{ $user_contact }}@else{{ 'N/A' }}@endif</i></span>
             <p>Should you have any questions regarding your order, please send an email to info@aahaas.com. Or contact us at +94 70 305 3731</p>
             <p>Have a great day!</p>
             <p>Team Aahaas</p>
         </div>
     </div>
 </body>

 </html>