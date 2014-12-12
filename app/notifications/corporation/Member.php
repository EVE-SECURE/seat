<?php
/*
The MIT License (MIT)

Copyright (c) 2014 eve-seat

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

namespace Seat\Notifications\Corporation;

use Seat\Notifications\BaseNotify;

class MemberStatus extends BaseNotify
{

    public static function update()
    {
        $recruiters = \Sentry::findAllUsersWithAccess('recruiter');

        foreach($recruiters as $recruiter) {
            $members = \EveCorporationMemberTracking::where('logoffDateTime', '<', \DB::raw('date_sub(NOW(), INTERVAL 1 MONTH)'))->get();
            foreach($members as $member){
                if(BaseNotify::canAccessCorp($recruiter->id, $member->corporationID)) {
        
                    $notification->user_id = $recruiter->id;
                    $notification->type = "Corporation";
                    $notification->title = "Member Inactivity";
                    $notification->text = $member->name . " has been inactive for over a month";
                    $hash = BaseNotify::makeNotificationHash($recruiter->id, $notification_type, $notification_title, $notification_text);
                    
                    $check = \SeatNotification::where('hash', '=', $hash)->exists();
                    
                    if(!$check) {
                        $notification = new \SeatNotification;
                        $notification->user_id = $recruiter->id;
                        $notification->type = $notification_type;
                        $notification->title = $notification_title;
                        $notification->text = $notification_text;
                        $notification->hash = $hash;
                        $notification->save(); 
                    }

                }
            }
        }
    }
}
