<?php
namespace Sercul\Messages;

use MobileOptin\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use MobileOptin\Models\User;


class MessagesController extends Controller {

    function __construct() {
        parent::__construct();
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index() {

        \SEOMeta::setTitle('Messages - page ' . ( \Input::get('page') ? \Input::get('page') : 1 ));

        \SEOMeta::setDescription('meta desc');
        \SEOMeta::addKeyword([ 'key1', 'key2', 'key3']);

        $user_id = Auth::id();

        if (Auth::user()->hasRole('admin')) {
            $data['messages'] = Messages::paginate(15)->setPath('messages');
        } else {
            $messagesRead = MessagesRead::where('user_id',Auth::user()->id)->with('message')->paginate(15);
            $data['messages'] = $messagesRead;
        }


        return view('sercul.messages.messages.list', $data);
    }

    public function add() {
        $data = array();
        \SEOMeta::setTitle('Add - Message ');
        \DB::enableQueryLog();

        $data['message'] = new \stdClass();
        $data['message']->id = 0;
        $data['message']->topic = \Input::old('topic');
        $data['message']->content = \Input::old('content');
        //        $data[ 'message' ]->status = \Input::old( 'status' );
        $data['message']->user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();


        return view('sercul.messages.messages.add_edit', $data);
    }

    public function edit($CId) {
        \SEOMeta::setTitle('Edit - Message');


        try {
            $message = Messages::where('id', '=', $CId)->firstOrFail();
        } catch (\Exception $e) {
            return redirect()->route('messages')->withError($e . 'Message not found or you do not have permissions');
        }

        return view('sercul.messages.messages.add_edit', ['message' => $message]);
    }

    public function upsert() {
        $validator = \Validator::make(\Input::only('id', 'topic'), [
                    'id' => 'required|integer',
                    'topic' => 'required'
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {

            $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();


            if (\Input::get('id') > 0) {
                $new_camp = Messages::where('id', '=', \Input::get('id'))->first();
                $new_camp->topic   = \Input::get( 'topic' );
                $new_camp->content   = \Input::get( 'content' );
                $new_camp->save();
                
                if (!$new_camp) {
                    return redirect()->back()->withInput()->withError('Message not found');
                }
            } else {
                $topic = \Input::get('topic');
                $content = \Input::get('content');

                \DB::transaction(function () use($content, $topic, $user_id) {
                    $new_camp = new Messages();
                    $new_camp->topic = $topic;
                    $new_camp->status = 0;
                    $new_camp->content = $content;
                    $new_camp->save();
                    $message_id = $new_camp->id;

                    $users = User::where('role_id', '!=', 1)->get();
                    foreach ($users as $user) {
                        $new_camp = new MessagesRead();
                        $new_camp->message_id = $message_id;
                        $new_camp->user_id = $user->id;
                        $new_camp->status = 0;
                        $new_camp->save();
                    }
                });
            }


            return redirect()->route('messages')->withNotify('Message saved');
        }
    }

    public function delete($CId) {
        try {
            if (Auth::user()->getOwner() == false) {
                $message = Messages::where('id', '=', $CId)->firstOrFail();
                $message->forceDelete();
                
                $messRead = MessagesRead::where('message_id',$CId)->delete();

                return redirect()->route('messages')->withSuccess('Message Deleted');
            }
        } catch (\Exception $e) {
            
        }
        return redirect()->route('messages')->withError('Message not removed ');
    }
    
    public function read($id) {
        try {
            $mes = MessagesRead::where('id',$id)->first();
            $mes->status = 1;
            $mes->save();
        } catch (\Exception $e) {
            return json_encode($e);
        }
          
        return '1';
    }

}
