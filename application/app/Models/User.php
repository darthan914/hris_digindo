<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email', 'username', 'password', 'active', 'id_role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getRole()
    {
        return $this->belongsTo('App\Models\Role', 'id_role');
    }

    public function getLeader()
    {
        return $this->belongsTo('App\User', 'leader');
    }

    public function hasAccess(string $permission) : bool
    {
        $permission_arr = explode(', ', $this->getRole->permission);
        $grant_arr      = explode(', ', $this->grant);
        $denied_arr     = explode(', ', $this->denied);

        if((in_array($permission, $permission_arr) || in_array($permission, $grant_arr)) && !in_array($permission, $denied_arr)) {
            return true;
        }
        return false;
    }

    public function setImpersonating($id)
    {
        Session::put('impersonate', $id);
        Session::put('original', Auth::id());

        Auth::logout();
        Auth::loginUsingId($id);
    }

    public function stopImpersonating()
    {
        Auth::logout();
        Auth::loginUsingId(Session::get('original'));

        Session::forget('impersonate');
        Session::forget('original');
    }

    public function isImpersonating()
    {
        return Session::has('impersonate');
    }

    public static function keypermission()
    {
        $array = 
        [
            // accessUser
            [
                'name' => 'User',
                'id' => 'user',
                'data' => 
                [
                    [
                        'name' => 'Semua User',
                        'value' => 'all-user'
                    ],
                    [
                        'name' => 'List',
                        'value' => 'list-user'
                    ],
                    [
                        'name' => 'Buat',
                        'value' => 'create-user'
                    ],
                    [
                        'name' => 'Edit',
                        'value' => 'edit-user'
                    ],
                    [
                        'name' => 'Hapus',
                        'value' => 'delete-user'
                    ],
                    [
                        'name' => 'Aktif',
                        'value' => 'active-user'
                    ],
                    [
                        'name' => 'Akses',
                        'value' => 'access-user'
                    ],
                    [
                        'name' => 'Ambil Ahli',
                        'value' => 'impersonate-user'
                    ],
                ]
            ],


            // accessRole
            [
                'name' => 'Role',
                'id' => 'role',
                'data' => 
                [
                    [
                        'name' => 'List',
                        'value' => 'list-role'
                    ],
                    [
                        'name' => 'Buat',
                        'value' => 'create-role'
                    ],
                    [
                        'name' => 'Edit',
                        'value' => 'edit-role'
                    ],
                    [
                        'name' => 'Hapus',
                        'value' => 'delete-role'
                    ],
                    [
                        'name' => 'Aktif',
                        'value' => 'active-role'
                    ],
                ]
            ],

            // accessEmployee
            [
                'name' => 'Employee',
                'id' => 'employee',
                'data' => 
                [
                    [
                        'name' => 'List',
                        'value' => 'list-employee'
                    ],
                    [
                        'name' => 'Buat',
                        'value' => 'create-employee'
                    ],
                    [
                        'name' => 'Lihat',
                        'value' => 'view-employee'
                    ],
                    [
                        'name' => 'Edit',
                        'value' => 'edit-employee'
                    ],
                    [
                        'name' => 'Hapus',
                        'value' => 'delete-employee'
                    ],
                    [
                        'name' => 'Buat Keluarga',
                        'value' => 'createFamily-employee'
                    ],
                    [
                        'name' => 'Edit Keluarga',
                        'value' => 'editFamily-employee'
                    ],
                    [
                        'name' => 'Hapus Keluarga',
                        'value' => 'deleteFamily-employee'
                    ],
                    [
                        'name' => 'Edit Kontrak',
                        'value' => 'editContract-employee'
                    ],
                    [
                        'name' => 'Hapus Kontrak',
                        'value' => 'deleteContract-employee'
                    ],
                    [
                        'name' => 'Edit Gaji',
                        'value' => 'editPayroll-employee'
                    ],
                    [
                        'name' => 'Hapus Gaji',
                        'value' => 'deletePayroll-employee'
                    ],
                ]
            ],

            // accessShift
            [
                'name' => 'Shift',
                'id' => 'shift',
                'data' => 
                [
                    [
                        'name' => 'List',
                        'value' => 'list-shift'
                    ],
                    [
                        'name' => 'Lihat',
                        'value' => 'view-shift'
                    ],
                    [
                        'name' => 'Buat',
                        'value' => 'create-shift'
                    ],
                    [
                        'name' => 'Edit',
                        'value' => 'edit-shift'
                    ],
                    [
                        'name' => 'Hapus',
                        'value' => 'delete-shift'
                    ],
                ]
            ],

            // accessDayoff
            [
                'name' => 'Cuti',
                'id' => 'dayoff',
                'data' => 
                [
                    [
                        'name' => 'List',
                        'value' => 'list-dayoff'
                    ],
                    [
                        'name' => 'Buat',
                        'value' => 'create-dayoff'
                    ],
                    [
                        'name' => 'Edit',
                        'value' => 'edit-dayoff'
                    ],
                    [
                        'name' => 'Hapus',
                        'value' => 'delete-dayoff'
                    ],
                    [
                        'name' => 'Konfirmasi',
                        'value' => 'confirm-dayoff'
                    ],
                ]
            ],

            // accessBorrow
            [
                'name' => 'Pinjam',
                'id' => 'borrow',
                'data' => 
                [
                    [
                        'name' => 'List',
                        'value' => 'list-borrow'
                    ],
                    [
                        'name' => 'Buat',
                        'value' => 'create-borrow'
                    ],
                    [
                        'name' => 'Edit',
                        'value' => 'edit-borrow'
                    ],
                    [
                        'name' => 'Hapus',
                        'value' => 'delete-borrow'
                    ],
                ]
            ],

            // accessLeave
            [
                'name' => 'Meninggalkan Kantor',
                'id' => 'leave',
                'data' => 
                [
                    [
                        'name' => 'List',
                        'value' => 'list-leave'
                    ],
                    [
                        'name' => 'Buat',
                        'value' => 'create-leave'
                    ],
                    [
                        'name' => 'Edit',
                        'value' => 'edit-leave'
                    ],
                    [
                        'name' => 'Hapus',
                        'value' => 'delete-leave'
                    ],
                    [
                        'name' => 'Konfirmasi',
                        'value' => 'confirm-leave'
                    ],
                ]
            ],

            // accessHoliday
            [
                'name' => 'Libur',
                'id' => 'holiday',
                'data' => 
                [
                    [
                        'name' => 'List',
                        'value' => 'list-holiday'
                    ],
                    [
                        'name' => 'Buat',
                        'value' => 'create-holiday'
                    ],
                    [
                        'name' => 'Edit',
                        'value' => 'edit-holiday'
                    ],
                    [
                        'name' => 'Hapus',
                        'value' => 'delete-holiday'
                    ],
                ]
            ],

            // accessOvertime
            [
                'name' => 'Lembur',
                'id' => 'overtime',
                'data' => 
                [
                    [
                        'name' => 'List',
                        'value' => 'list-overtime'
                    ],
                    [
                        'name' => 'Buat',
                        'value' => 'create-overtime'
                    ],
                    [
                        'name' => 'Edit',
                        'value' => 'edit-overtime'
                    ],
                    [
                        'name' => 'Hapus',
                        'value' => 'delete-overtime'
                    ],
                    [
                        'name' => 'Konfirmasi',
                        'value' => 'confirm-overtime'
                    ],
                ]
            ],

            // accessBorrow
            [
                'name' => 'Absen',
                'id' => 'absence',
                'data' => 
                [
                    [
                        'name' => 'List',
                        'value' => 'list-absence'
                    ],
                    [
                        'name' => 'Lihat',
                        'value' => 'view-absence'
                    ],
                    [
                        'name' => 'Buat',
                        'value' => 'create-absence'
                    ],
                    [
                        'name' => 'Edit',
                        'value' => 'edit-absence'
                    ],
                    [
                        'name' => 'Hapus',
                        'value' => 'delete-absence'
                    ],
                ]
            ],

        ];

        return $array;
    }
}
