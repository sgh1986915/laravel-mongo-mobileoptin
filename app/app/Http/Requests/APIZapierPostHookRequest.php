<?php namespace MobileOptin\Http\Requests;

use MobileOptin\Http\Requests\Request;

class APIZapierPostHookRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'api_key'	    => 'required|exists:integrations_user,local_api_key',
			'target_url'    => 'required|url',
            'event'         => '',
		];
	}

}
