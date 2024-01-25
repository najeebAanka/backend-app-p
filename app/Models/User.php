<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }

    public function sellsHorses()
    {
        return Horse::where('seller_id', $this->id)->where('status', 'accepted')->count() > 0;
    }

    public static function sendNotificationToAll($type, $target, $message, $object)
    {
        if (!config('app.debug')) {
            $SERVER_API_KEY = "";

            $body = new \stdClass();
            $body->type = $type;
            $body->target = $target;
            $body->message = $message;
            $body->object = $object;

            $data = [
                "to" => "/topics/all",
                "notification" => [
                    "title" => "News from test",
                    "body" => $message,
                ],
                "data" => $body
            ];
            $dataString = json_encode($data);

            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);

            $n = new Notification();
            $n->user_id = "-1";
            $n->not_type = $type;
            $n->not_id = $target;
            $n->gen_text = $message;
            $n->save();

            return $response;
        }
        return "ok - testing";
    }


    public function sendNotification($type, $target, $message, $object)
    {
        if (!config('app.debug')) {
            if ($this->fcm_token) {
                $firebaseToken = [$this->fcm_token];

                $SERVER_API_KEY = "";

                $body = new \stdClass();
                $body->type = $type;
                $body->target = $target;
                $body->message = $message;
                $body->object = $object;

                $data = [
                    "registration_ids" => $firebaseToken,
                    "notification" => [
                        "title" => "News from test",
                        "body" => $message,
                    ],
                    "data" => $body
                ];
                $dataString = json_encode($data);

                $headers = [
                    'Authorization: key=' . $SERVER_API_KEY,
                    'Content-Type: application/json',
                ];

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

                $n = new Notification();
                $n->user_id = $this->id;
                $n->not_type = $type;
                $n->not_id = $target;
                $n->gen_text = $message;
                $n->save();

                $response = curl_exec($ch);
                return $body;
            } else {
                return "FCM token was not updated or null for user " . $this->id . " [" . $this->fcm_token . "]";
            }
        } else {
            return "ok - testing";
        }
    }
}
