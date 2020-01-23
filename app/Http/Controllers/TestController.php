<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Brand;
use App\Offer;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;
use Google_Client; 

class TestController extends Controller
{
	public function offers()
    {
        $offers = Offer::paginate(10);

        return response()->json($offers);
    }

    public function firebaseIDToken(){
		$idTokenString = 'eyJhbGciOiJSUzI1NiIsImtpZCI6ImJhZDM5NzU0ZGYzYjI0M2YwNDI4YmU5YzUzNjFkYmE1YjEwZmZjYzAiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiIxODAzMTY4MDA0MTYtZWprdGt1cXJiOWNxNGM2dTJmOWI1dnQxcGJ2bmozMXMuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiIxODAzMTY4MDA0MTYtY2tram5mMjdycTJkYzJvNW85bHRmdmhwNHFibnZqOWUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMTM0MDgwMTA3NDY5OTA2NTYxNjkiLCJlbWFpbCI6Imxpa2l0YW9mMjAxNUBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibmFtZSI6Ikxpa2l0YSBNYWhhcmphbiIsInBpY3R1cmUiOiJodHRwczovL2xoMy5nb29nbGV1c2VyY29udGVudC5jb20vYS0vQUF1RTdtQlJ2QlpQUmxzc2ZpVUZ1bmZOQ2p3YWFrUUNuNTNqbDJlMHdxNzFvZz1zOTYtYyIsImdpdmVuX25hbWUiOiJMaWtpdGEiLCJmYW1pbHlfbmFtZSI6Ik1haGFyamFuIiwibG9jYWxlIjoiZW4iLCJpYXQiOjE1Nzk3NjAxNzQsImV4cCI6MTU3OTc2Mzc3NH0.V_QD8wxBPpuwmiX53wYKdMc34aHY33xHaoPQxv2Y3v2mGDG2Dp42SKykGKVfvBXJ_-7KwxDP61WUwRyjFOsoDAh675DDeUccE4IQpKyjWZbipLMdqM6TijTSKk29BO48blkZgymNQn1HWZh5x-bmbHovccEZC-cde047QHrhtFVsVG7FX2pgiPGDIhtxL5nLI2CrkItktq5o0PCk_KKE7-DSyuCYdAGStFHKypCItvOifSGlw6SjCVbG5PR6vtYQF7U-k3TTREEHF-0iQeR-XGN8UxAhCAzl8cY_mn26yY8LV8oAYdb94jcveaS2P2FnAkznbkNBan1fHYFy29tkIw';

		// $url = 'https://oauth2.googleapis.com/tokeninfo?id_token='.$idTokenString;
 
		//Use file_get_contents to GET the URL in question.
		
		// try {
		//     $contents = file_get_contents($url);
		// } catch (\ErrorException $e) {
		//     return 'The token could not be parsed: '.$e->getMessage();
		// } catch (InvalidToken $e) {
		//     return 'The token is invalid: '.$e->getMessage();
		// }



		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/tokeninfo?id_token='.$idTokenString);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
 		curl_setopt($ch, CURLOPT_HTTP200ALIASES, (array)400);

		$headers = array();
		$headers[] = "Accept: application/json";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		
		if (!curl_errno($ch)) {
		  switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
		    case 200:  # OK
		      break;
		    default:
		      echo 'Unexpected HTTP code: ', $http_code, "\n";
		  }
		}
		if (curl_errno($ch)) {
		    echo 'Error:' . curl_error($ch);
		}
		curl_close ($ch);

		return json_decode($result, true);
		


	}
}
