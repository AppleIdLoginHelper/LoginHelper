<?php
namespace app\controller;

use think\helper\Str;
use think\facade\Env;
use think\facade\View;
use app\BaseController;
use app\model\MailList;
use app\model\MailContent;

class Index extends BaseController
{
    function getClient()
    {
        $path = dirname(dirname(dirname(__FILE__)));
        
        $client = new \Google_Client();
        $client->setApplicationName('Gmail API PHP Quickstart');
        $client->setScopes(\Google_Service_Gmail::GMAIL_READONLY);
        $client->setAuthConfig($path . '/credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        $tokenPath = $path . '/token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            }
        }

        return $client;
    }

    public function test()
    {
        // 开了 oauth2.0 的 gmail 账户 也是接收其他 gmail 转发邮件的收件箱
        $email_address = '';

        // '你买的有google voice号的gmail账户' => '(对应下载的软件名称或其他备注) 绑定那个gv号的apple id账户',
        
        $email_correspondence = [
            '' => '',
        ];
        
        $client = self::getClient();
        $httpClient = $client->authorize();

        // https://developers.google.com/gmail/api/reference/rest/v1/users.messages/list

        $list = $httpClient->get('https://gmail.googleapis.com/gmail/v1/users/'.$email_address.'/messages?maxResults=10&q=from: txt.voice.google.com Apple ID');
        $response = $list->getBody();
        $object = json_decode($response, true);
        
        foreach ($object['messages'] as $data)
        {
            $id = $data['id'];
            if (MailList::where('thread_id', $id)->find() == null) {
                $record = new MailList;
                $record->thread_id = $id;
                $record->created_at = time();
                $record->save();
            }

            if (MailContent::where('thread_id', $id)->find() == null) {
                // https://developers.google.com/gmail/api/reference/rest/v1/users.messages
                
                // get
                $body = $httpClient->get('https://gmail.googleapis.com/gmail/v1/users/'.$email_address.'/messages/'.$id);
                $response = $body->getBody();
                $object = json_decode($response, true);
                preg_match_all('!\d+!', $object['snippet'], $matches);
                
                // process data

                foreach ($object['payload']['headers'] as $item)
                {
                    // 收信时间
                    if ($item['name'] == 'Date') {
                        $receive_time = $item['value'];
                        $receive_time = date('Y-m-d H:i:s', strtotime($receive_time));
                    }

                    // 原始收信人
                    if ($item['name'] == 'Delivered-To') {
                        $original_recipient = $item['value'];
                    }
                }
                
                // save
                if (!Str::contains($object['snippet'], '@apple.com') || (Str::contains($object['snippet'], '@apple.com') && Env::get('OTHER.BLOCK_WEBSITE_LOGIN') == false)) {
                    if (!Str::contains($object['snippet'], '@icloud.com') || (Str::contains($object['snippet'], '@icloud.com') && Env::get('OTHER.BLOCK_ICLOUD_LOGIN') == false)) {
                        $record = new MailContent;
                        $record->thread_id          = $id;
                        $record->receive_time       = $receive_time;
                        $record->original_recipient = $original_recipient;
                        $record->correspondence     = $email_correspondence[$original_recipient] ?? 'unknow';
                        $record->original_text      = $object['snippet'] ?? 'null';
                        $record->snippet            = $matches['0']['0'] ?? 'null';
                        $record->created_at         = time();
                        $record->save();
                    }
                }
            }
        }

        $msg = MailContent::limit(10)
        ->order('receive_time', 'desc')
        ->select();
        
        View::assign('msg', $msg);
        View::assign('auth_refresh', Env::get('OTHER.AUTO_REFRESH'));
        View::assign('refresh_interval', Env::get('OTHER.REFRESH_INTERVAL'));
        return View::fetch('../app/view/index.html');
    }

    public function callback()
    {
        return View::fetch('../app/view/callback.html');
    }
}
