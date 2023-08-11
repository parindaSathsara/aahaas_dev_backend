<!DOCTYPE html>

<body>

    <div class="main__Box" style="width: 794px;height:auto;box-sizing:border-box;box-shadow: 0px 0px 2px 0px rgba(0, 0, 0, .1);margin-left:-50px;font-family:'Times New Roman';">
        <img src="https://i.ibb.co/dBQHZWQ/aahaas.png" style="text-align:right;margin-left:670px;margin-top:30px;" width="100" height="30" alt="aahaas_logo" class="logo__aahaas" />
        <div class="top__Box" style="margin-top: -70px;margin-left:10px;margin-bottom:10px;display:flex;flex-direction:row;justify-content:space-between;">
            <div style="line-height: .5rem; margin:20px;">
                <p style="text-align: left;color:#137dcf;font-size:22px;">Your booking is confirmed</p>
                <p style="text-align: left;color:#000;"><b>Booking Reference: <span style="color:#000">{{$booking_ref}}</span></b></p>
                <p style="text-align: left;color:#000;"><b>Confirmation Reference: <span style="color:#000">{{$confirm_num}}</span></b></p>

            </div>
        </div>
        <hr style="margin-left:30px;width: 400px;margin-top:-30px;">
        <!-- <p style="margin:20px"><b>E-TICKET</b></p> -->

        <div style="margin:30px;text-align:justify;">
            <p>Dear {{$reservation_name}},</p>
            <p>You're all set for your trip! This email has all the details of your booking on aahaas.com. Remember to print your booking ticket or, besure you have easy access to them when you travel.</p>
        </div>

        <div style="margin:30px;text-align:justify; background-color:#e5eaf0;padding:5px;">
            <p style="margin:10px;">Please be at the airport counter at least 90 minutes (or in good time) before your flight departure, If you're unable to print your boarding pass(es), or if you have bags to check in. Your check-in may be cancelled and
                your seat re-assigned if you do not collect or print your boarding pass(es) before the check-in counter close.
            </p>
        </div>

        <div class="center__FlightDetails table table-striped" style="padding: 30px;margin-top:-20px;">
            <h3>Flight Details</h3>
            <p style="text-align: right;color:#000;"><b>Airline Booking Reference: <span style="color:#000">{{$booking_ref}}</span></b></p>
            <table style="border-collapse:collapse;text-align:center;">
                <thead style="background-color:#00276a; font-size:12px;color:#fff;">
                    <tr>
                        <th style="padding: 10px;border-right:1px solid #fff;">FLIGHT</th>
                        <th style="padding: 10px;border-right:1px solid #fff;">DEPARTURE</th>
                        <th style="padding: 10px;border-right:1px solid #fff;">FROM</th>
                        <th style="padding: 10px;">ARRIVAL</th>
                        <th style="padding: 10px;border-right:1px solid #fff;">TO</th>
                    </tr>
                </thead>
                <tbody style="font-size:14px;text-align:left;">

                    @foreach ($reserveData as $ticketData)
                    <tr>
                        <td style="padding:10px; text-align:left;background-color:#d9d9d9;" colspan="5"><b>{{strstr($ticketData->departure_fromTitle,'-',true)}} to {{strstr($ticketData->arrival_toTitle,'-',true)}} - Total Flying Hours: <span>{{intdiv($ticketData->total_duration,60).'hrs '.($ticketData->total_duration%60).'mins'}}</span></b></td>
                    </tr>
                    @if($ticketData->hidden_stopCode != null)
                    <tr>
                        <td style="padding: 10px;">{{$ticketData->flight_code}} - {{$ticketData->flight_no}}<br><span style="color:#137dcf">{{$ticketData->flight_title}}</span></td>
                        <td style="padding: 10px;">{{date('l', strtotime($ticketData->departure_time))}}, {{$ticketData->departure_time}} </td>
                        <td style="padding: 10px;">({{$ticketData->departure_fromCode}}) {{$ticketData->departure_fromTitle}} {{$ticketData->dep_terminal}}</td>
                        <td style="padding: 10px;">{{date('l', strtotime($ticketData->hidden_stopArrival))}}, {{$ticketData->hidden_stopArrival}}:00</td>
                        <td style="padding: 10px;">({{$ticketData->hidden_stopCode}}) {{$ticketData->hidden_stopTitle}}</td>
                    </tr>
                    <!-- #### -->
                    <tr>
                        <td style="padding: 10px;">{{$ticketData->flight_code}} - {{$ticketData->flight_no}}<br><span style="color:#137dcf">{{$ticketData->flight_title}}</span></td>
                        <td style="padding: 10px;">{{date('l', strtotime($ticketData->hidden_stopDeparture))}}, {{$ticketData->hidden_stopDeparture}}</td>
                        <td style="padding: 10px;">({{$ticketData->hidden_stopCode}}) {{$ticketData->hidden_stopTitle}}</td>
                        <td style="padding: 10px;">{{date('l', strtotime($ticketData->arrival_time))}}, {{$ticketData->arrival_time}}</td>
                        <td style="padding: 10px;">({{$ticketData->arrival_toCode}}) {{$ticketData->arrival_toTitle}} {{$ticketData->arr_terminal}}</td>
                    </tr>
                    @else
                    <tr>
                        <td style="padding: 10px;">{{$ticketData->flight_code}} - {{$ticketData->flight_no}}<br><span style="color:#137dcf">{{$ticketData->flight_title}}</span></td>
                        <td style="padding: 10px;">{{date('l', strtotime($ticketData->departure_time))}}, {{$ticketData->departure_time}}</td>
                        <td style="padding: 10px;">({{$ticketData->departure_fromCode}}) {{$ticketData->departure_fromTitle}} {{$ticketData->dep_terminal}}</td>
                        <td style="padding: 10px;">{{date('l', strtotime($ticketData->arrival_time))}}, {{$ticketData->arrival_time}}</td>
                        <td style="padding: 10px;">({{$ticketData->arrival_toCode}}) {{$ticketData->arrival_toTitle}} {{$ticketData->arr_terminal}}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding:10px; text-align:left;background-color:#d9d9d9;" colspan="5"><b>Other Information</b></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px;" colspan="5">
                            <b>Baggage:</b> {{$ticketData->baggage_details}} Kg(s) &nbsp;|&nbsp; <b>Class:</b> <span style="color:#137dcf;">{{$ticketData->flight_class}}</span> &nbsp;
                        </td>
                    </tr>
                    @endforeach
                    <!-- <tr>
                        <td style="padding:10px; text-align:left;background-color:#d9d9d9;" colspan="5"><b>Colombo to Dubai - Total Flying Hours: <span>10hrs 45mins</span></b></td>
                    </tr>


                    <tr>
                        <td style="padding: 10px;">UL-225<br><span style="color:#137dcf">Sri Lankan Airlines</span></td>
                        <td style="padding: 10px;">Monday, 2022-11-22 15:40:00</td>
                        <td style="padding: 10px;">(CMB) Bandaranayake International Airport</td>
                        <td style="padding: 10px;">Monday, 2022-11-22 18:40:00</td>
                        <td style="padding: 10px;">(DXB) Dubai International Airport | Terminal 2</td>
                    </tr>

                    <tr>
                        <td style="padding:10px; text-align:left;background-color:#d9d9d9;" colspan="5"><b>Colombo to Dubai - Total Flying Hours: <span>10hrs 45mins</span></b></td>
                    </tr>

                    <tr>
                        <td style="padding:10px; text-align:left;background-color:#d9d9d9;" colspan="5"><b>Other Information</b></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px;" colspan="5">
                            <b>Baggage:</b> 35 Kg(s) &nbsp;|&nbsp; <b>Class:</b> <span style="color:#137dcf;">Economy</span> &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px; text-align:left;background-color:#d9d9d9;" colspan="5"><b>Dubai to Colombo - Total Flying Hours: <span>10hrs 45mins</span></b></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px;">EK-320<br><span style="color:#137dcf">Emirates Airlines</span></td>
                        <td style="padding: 10px;">Tuesday, 2022-11-22 15:40:00</td>
                        <td style="padding: 10px;">(DXB) Dubai International Airport | Terminal 2</td>
                        <td style="padding: 10px;">Tuesday, 2022-11-22 18:40:00</td>
                        <td style="padding: 10px;">(CMB) Bandaranayake International Airport</td>
                    </tr>

                    <tr>
                        <td style="padding:10px; text-align:left;background-color:#d9d9d9;" colspan="5"><b>Other Information</b></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px;" colspan="5">
                            <b>Baggage:</b> 35 Kg(s) &nbsp;|&nbsp; <b>Class:</b> <span style="color:#137dcf;">Economy</span> &nbsp;|&nbsp; <b>Booking Status:</b> <span style="color:#137dcf;">Confirmed</span>
                        </td>
                    </tr> -->
                </tbody>
            </table>
            <hr>
            <!-- <h4 style="text-align:left;color:#137dcf;margin-top:30px;">Traveller(s) Details</h4>
            <table style="border-collapse:collapse;text-align:center;">
                <thead style="background-color:#00276a; font-size:12px;color:#fff;">
                    <tr>
                        <th style="padding: 10px;border-right:1px solid #fff;">NAME</th>
                        <th style="padding: 10px;border-right:1px solid #fff;">TYPE</th>
                        <th style="padding: 10px;border-right:1px solid #fff;">DOB</th>
                        <th style="padding: 10px;border-right:1px solid #fff;">PASSPORT NO</th>
                    </tr>
                </thead>

                <tbody style="font-size:14px;text-align:left;">
                    <tr>
                        <td style="padding: 10px;">Viraj Kavinda Meegahapola</td>
                        <td style="padding: 10px;">Adult</td>
                        <td style="padding: 10px;">1995-03-20</td>
                        <td style="padding: 10px;">N5546357</td>
                    </tr>
                </tbody>
            </table>
            <hr> -->
            <h4 style="text-align:left;color:#137dcf;margin-top:30px;">Additional Information</h4>
            <div>
                <h4 style="margin-bottom:-5px;">Privacy Policy</h4>
                <p style="text-align:justify;">The information you gave us to is protected by our privacy policy, However, in some circumtances, government regulations may require us to provide information or permit access to our
                    passenger/customer data.
                </p>

                <h4 style="margin-bottom:-5px;">Valid Travel Documents</h4>
                <p style="text-align:justify;">Each passenger must hold a valid passport (and visa(s), if required) to be allowed entry into each destination on the flight itinerary. Aahaas cannot be held responsible if
                    a passenger is denied entry and/or deported by any local authority.
                </p>
            </div>
        </div>
    </div>

</body>


</html>