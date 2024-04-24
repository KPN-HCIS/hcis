<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class SsoController extends Controller
{
    function dbauth(Request $request)
    {
    $encryptedData = $request->data;
    // $encryptedData = "U098WlRhcEZUdX8Af1tgT1duZEBUXwNdbG5SXmdxQkFUXwdcVAV8QXpbeEBUZX9Ff1hkRlRhYExScXBCVXV%2FAHtiVU57XHcGe2JdAnllQV9XcXBMV3V%2FAH9beFxsW2cDe3JvTHtcY054cnBeeARjA29hb09sYWRce2JdQXhyfF97cWNMb1tvTmxyXl1vXFJfbHJgX2xyewV4cXhfbGFnAntcbwR7XHxce1xjQXhbewJ4XFpee0xjTGxib09sXFJae2JjB2xcVUF7W2Rbe0x3QWxccFt4cXBeeExSW2xyXU5vYmNMe3J7AmxbfwZvYXNPeXJwXHtMYwRscl5fewRnBXhydF1%2FX0FfYGFaXX9cWV97Yn9Mf19BX2xhB0FUcQ8DbGFgUFRbDl95X39Be2JzTnlidwR7cndBe09%2FRX9beEBUbnReVFhaUFRbcEJsZX8Af11CZ2JfdHJUBXxBVAV8XlJxWkBUX39Ff1tsRlVYeAZUW3BCbGV%2FAH9dYE9XbmRAVF90e29uZAZXZXRzbG5SXn9fQV9UcXBMUnEDXlRhY195X39fenV8QGxbbEZvBGBQVGEPX1dhTlp%2FXFlff19BX1QEbFtXYXhabgROQG8EcAZXYQ9DbgR4RlJ%2BXV95X3x9b2FCXlVYZF5%2FcHQHVQRwBn9fQV9UBGxbV2F4Wm4ETkBvBHAGV2EPQ24FdEZUW3hAbHFjX3lff196dXwGVARCWlRffwB%2FXH8CbFx%2FTHtcdF97cntMb0xjQXhcXUx7YXxfeHJ0X3thZF5sYlVPb2J%2FTH9YBgs%3D";

    // Dekripsi data yang diterima menggunakan Base64
    $decodedData = base64_decode($encryptedData);

    // Key untuk XOR Decryption
    $key = '666666';

    // Dekripsi data yang telah didecode dengan XOR Decryption
    $decryptedDataxor = $this->xorDecrypt($decodedData, $key);

    // Dekripsi data yang diterima menggunakan Base64
    $decryptedData = base64_decode($decryptedDataxor);

    $decryptedDataArray = json_decode($decryptedData, true);
    //dd($decryptedDataArray);

    $email = $decryptedDataArray['email'];
    $token = $decryptedDataArray['token'];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://kpncorporation.darwinbox.com/checkToken',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(array(
            "api_key" => "3bbfc6dfa28df2a81bd45192bf4f96b72628ae0ec9921a062aef937b7f25d6c704ccfc9539e70e5939a45cc43f3b7ce61477c7135a83bdbd6f85d5c38b5fc563",
            "token" => $token, // Menggunakan nilai token yang telah didekripsi sebelumnya
        )),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic S1BOX1NTTzpUTXNfJDU2T3BzJXB3',
            'Cookie: __cf_bm=4uUEj1zmjV.MExppSaO8PotAtVYX3j1LC37K7VZbRrA-1712303016-1.0.1.1-t6I22efQWtYGVIwVMpn7P63eop_5tmi8pU7n_ju6i2_AD1YM846eQF2VlfbZKoC.ZwvzWCyaXDISwvp.JP2TPQ; _cfuvid=kEL.TVWTCuZAsepIdMuvd7X9.q7rTz4SP9.769IZWFQ-1712126032738-0.0.1.1-604800000; session=83c35e478c2cafccac60fced59ff2f30'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $responseData = json_decode($response, true);

    // Ambil nilai status
    $status = $responseData['status'];

    if($status==1){
        $user = User::where('email', $email)->first(); // Cari user berdasarkan email
        if ($user) {
            Auth::login($user);
            $user->token = $token; // Update nilai token
            $user->email_log = $email;
            $user->save(); // Simpan perubahan

            // Regenerate session untuk mencegah serangan CSRF
            $request->session()->regenerate();

            // Redirect ke halaman setelah login
            Alert::success('Welcome '.Auth::user()->name, 'You have successfully logged in via Darwinbox!')->showConfirmButton('OK');
            return redirect()->intended(route('home', absolute: false));
            
        }else{
            Alert::error('Login Failed, Please Contact Administrator')->showConfirmButton('OK');
            return redirect('https://kpncorporation.darwinbox.com/');
        }
    }else{
        Alert::error('Login Failed, Please Contact Administrator')->showConfirmButton('OK');
        return redirect('https://kpncorporation.darwinbox.com/');
    }
    
    }

    private function xorDecrypt($data, $key) {
        $keyLength = strlen($key);
        $dataLength = strlen($data);
        $decrypted = '';
    
        // Loop melalui data dan melakukan XOR dengan key
        for ($i = 0; $i < $dataLength; $i++) {
            $decrypted .= $data[$i] ^ $key[$i % $keyLength];
        }
    
        return $decrypted;
    }
}
