<?php


namespace App\Services;


use App\Models\Notification;

class NotificationService
{
    public static function addNotification($user_id, $equipment_id = null, $client_id = null, $sim_card_id = null, $message)
    {
        $query = Notification::query();
        $query->withTrashed();


       if ($equipment_id){
           $query->where('equipment_id', $equipment_id);
       }

       if ($client_id){
           $query->where('client_id', $client_id);
       }

       // if ($sim_card_id){
       //     $query->where('sim_card_id', $sim_card_id);
       // }

        $n = $query->first();
        // var_dump($n);exit;
        //
    //    if (!$n) {

            $notification = new Notification();

            if ($user_id) {
                $notification->user_id = $user_id;
            }

            if ($equipment_id) {
                $notification->equipment_id = $equipment_id;
            }

            if ($client_id) {
                $notification->client_id = $client_id;
            }

            if ($sim_card_id) {
                $notification->sim_card_id = $sim_card_id;
            }

            $notification->message = $message;
            $notification->status = true;
            $notification->save();

        }
    //}
}
