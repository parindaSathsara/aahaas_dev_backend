<?php

namespace App\Http\Controllers\LifeStyles;

use App\Http\Controllers\Controller;
use App\Models\LifeStyle\LifeStyleVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class LifeStyleVendorController extends Controller
{
    public function getLifeStyleTypes()
    {
        $LifeStyleTypes = DB::table('tbl_lifestyle')->select('lifestyle_attraction_type')->groupBy('lifestyle_attraction_type')->get();

        $list = array();

        foreach ($LifeStyleTypes as $type) {
            $list[] = $type->lifestyle_attraction_type;
        }

        return response(['status' => 200, 'lifestyletypedata' => $list]);
    }

    public function createNewLifeStyleVendor(Request $request)
    {
        $currentTime = \Carbon\Carbon::now()->toDateTimeString();

        $EmailCount = $request->input('emailCount');
        $ContactCount = $request->input('contactNumberCount');

        $SellerName = $request['sellername'];
        $LifeStyleType = $request['businessname'];
        $TypeName = $request['businesstype'];
        $LocationAddress = $request['locationaddress'];
        $EmailOne = $request['email1'];
        $ContactOne = $request['contact1'];
        $KeyContact = $request['keycontactname'];
        $KeyContactEmail = $request['keycontactemail'];
        $KeyContactNum = $request['keycontactnumber'];
        $AdditionalEmail = $request['additionalEmail'];
        $AdditionalContact = $request['additionalContact'];
        $UserId = $request['userid'];

        $validator = Validator::make($request->all(), [
            'sellername' => 'required',
            'businessname' => 'required',
            'businesstype' => 'required',
            'locationaddress' => 'required',
            'email1' => 'required',
            'contact1' => 'required',
            'keycontactname' => 'required',
            'keycontactemail' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'validation_error' => $validator->messages()
            ]);
        } else {
            $newLifeStyleVendor = LifeStyleVendor::create([
                'seller_name' => $SellerName,
                'lifestyle_type' => $LifeStyleType,
                'type_name' => $TypeName,
                'official_address' => $LocationAddress,
                'official_email1' => $EmailOne,
                'additional_email' => $AdditionalEmail,
                'contact_number1' => $ContactOne,
                'additional_contact' => $AdditionalContact,
                'key_contact_name1' => $KeyContact,
                'key_contact_email1' => $KeyContactEmail,
                'key_contact_number1' => $KeyContactNum,
                'key_contact_name2' => '-',
                'key_contact_email2' => '-',
                'key_contact_number2' => '-',
                'created_at' => $currentTime,
                'user_id' => $UserId
            ]);

            return response(['status' => 200, 'newlifestylevendor' => $newLifeStyleVendor]);
        }
    }
}
