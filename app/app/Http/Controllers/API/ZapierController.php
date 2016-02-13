<?php namespace MobileOptin\Http\Controllers\API;

use MobileOptin\Models\IntegrationsUser;
use MobileOptin\Models\ZapierWebhook;
use MobileOptin\Http\Requests;
use MobileOptin\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ZapierController extends Controller {

    // accessed by Zapier after a user enters their API key to verify that it works
	public function test()
    {
        return response()->json(
            [
                'status' => true,
            ]
        );
    }

    // handle Zapier REST Hook subscription request
    public function postHook(Requests\APIZapierPostHookRequest $request)
    {
        // add hook to database - this saves the url to be called when the corresponding integration is triggered
        $integration = IntegrationsUser::whereLocalApiKey($request->api_key)->firstOrFail();
        $zapier_webhook = new ZapierWebhook();
        $zapier_webhook->url = $request->target_url;
        $integration->zapier_webhooks()->save($zapier_webhook);

        return response()->json(
            [
                'id' => $zapier_webhook->id,
            ]
        );
    }

    // handle Zapier Polling request with sample data for setting up new Zaps
    public function getHookSample()
    {
        return response()->json(
            [
                [
                    "name"          => "User Name",
                    "email"         => "email@example.com",
                    "campaign_id"   => 1,
                    "campaign_name" => "Campaign Name",
                    "template_id"   => 1,
                    "template_name" => "Template Name",
                    /*
                    "dayOfCycle"    => 0,
                    "campaign"      => [
                        "campaignId" => 1
                    ],
                    */
                ]
            ]
        );
    }
}
