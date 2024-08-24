function cashadvancedEdit($key)
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = CATransaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();
        // $transactions = CATransaction::find($key);
        $transactions = CATransaction::findByRouteKey($key);
        // dd($key);
        return view('hcis.reimbursements.cashadv.editCashadv', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $transactions,
        ]);
    }
    function cashadvancedUpdate(Request $req, $key)
    {
        $userId = Auth::id();
        $uuid = Str::uuid();
        $model = CATransaction::findByRouteKey($key);
        $employee_data = Employee::where('id', $userId)->first();
        // $model->type_ca = $req->ca_type;
        $model->no_ca = $req->no_ca;
        $model->no_sppd = $req->bisnis_numb;
        // $model->user_id         = $req->id;
        $model->unit = $req->unit;
        $model->contribution_level_code = $req->companyFilter;
        $model->destination = $req->locationFilter;
        $model->others_location = $req->others_location;
        $model->ca_needs = $req->ca_needs;
        $model->start_date = $req->start_date;
        $model->end_date = $req->end_date;
        $model->date_required = $req->ca_required;
        $model->declare_estimate = $req->ca_decla;
        $model->total_days = $req->totaldays;
        if ($req->ca_type == 'dns') {
            // Menyiapkan array untuk menyimpan detail dari setiap bagian
            $detail_perdiem = [];
            $detail_transport = [];
            $detail_penginapan = [];
            $detail_lainnya = [];

            // Loop untuk Perdiem
            if ($req->has('start_bt_perdiem')) {
                // $totalPerdiem = str_replace('.', '', $req->total_bt_perdiem[]);
                foreach ($req->start_bt_perdiem as $key => $startDate) {
                    $endDate = $req->end_bt_perdiem[$key];
                    $totalDays = $req->total_days_bt_perdiem[$key];
                    $location = $req->location_bt_perdiem[$key];
                    $other_location = $req->other_location_bt_perdiem[$key];
                    $companyCode = $req->company_bt_perdiem[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_perdiem[$key]);
                    // $totalPerdiem = str_replace('.', '', $req->total_bt_perdiem[]);

                    $for_perdiem[] = [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'total_days' => $totalDays,
                        'location' => $location,
                        'other_location' => $other_location,
                        'company_code' => $companyCode,
                        'nominal' => $nominal,
                    ];
                }
            }

            // Loop untuk Transport
            if ($req->has('tanggal_bt_transport')) {
                foreach ($req->tanggal_bt_transport as $key => $tanggal) {
                    $keterangan = $req->keterangan_bt_transport[$key];
                    // $companyCode = $req->company_bt_transport[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_transport[$key]);

                    $detail_transport[] = [
                        'tanggal' => $tanggal,
                        'keterangan' => $keterangan,
                        'company_code' => $companyCode,
                        'nominal' => $nominal,
                    ];
                }
            }

            // Loop untuk Penginapan
            if ($req->has('start_bt_penginapan')) {
                foreach ($req->start_bt_penginapan as $key => $startDate) {
                    $endDate = $req->end_bt_penginapan[$key];
                    $totalDays = $req->total_days_bt_penginapan[$key];
                    $hotelName = $req->hotel_name_bt_penginapan[$key];
                    $companyCode = $req->company_bt_penginapan[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_penginapan[$key]);

                    $detail_penginapan[] = [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'total_days' => $totalDays,
                        'hotel_name' => $hotelName,
                        'company_code' => $companyCode,
                        'nominal' => $nominal,
                    ];
                }
            }

            // Loop untuk Lainnya
            if ($req->has('tanggal_bt_lainnya')) {
                foreach ($req->tanggal_bt_lainnya as $key => $tanggal) {
                    $keterangan = $req->keterangan_bt_lainnya[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_lainnya[$key]);

                    $detail_lainnya[] = [
                        'tanggal' => $tanggal,
                        'keterangan' => $keterangan,
                        'nominal' => $nominal,
                    ];
                }
            }

            // Konversi array menjadi JSON untuk disimpan di database
            $detail_ca = [
                'detail_perdiem' => $detail_perdiem,
                'detail_transport' => $detail_transport,
                'detail_penginapan' => $detail_penginapan,
                'detail_lainnya' => $detail_lainnya,
            ];

            $model->detail_ca = json_encode($detail_ca);
        } else if ($req->ca_type == 'ndns') {
            $detail_ndns = [];
            if ($req->has('tanggal_nbt')) {
                foreach ($req->tanggal_nbt as $key => $tanggal) {
                    $keterangan_nbt = $req->keterangan_nbt[$key];
                    $nominal_nbt = str_replace('.', '', $req->nominal_nbt[$key]); // Menghapus titik dari nominal sebelum menyimpannya

                    $detail_ndns[] = [
                        'tanggal_nbt' => $tanggal,
                        'keterangan_nbt' => $keterangan_nbt,
                        'nominal_nbt' => $nominal_nbt,
                    ];
                }
            }
            $detail_ndns_json = json_encode($detail_ndns);
            $model->detail_ca = $detail_ndns_json;
        } else if ($req->ca_type == 'entr') {
            $detail_e = [];
            $relation_e = [];

            // Mengumpulkan detail entertain
            if ($req->has('enter_type_e_detail')) {
                foreach ($req->enter_type_e_detail as $key => $type) {
                    $fee_detail = $req->enter_fee_e_detail[$key];
                    $nominal = str_replace('.', '', $req->nominal_e_detail[$key]); // Menghapus titik dari nominal sebelum menyimpannya

                    $detail_e[] = [
                        'type' => $type,
                        'fee_detail' => $fee_detail,
                        'nominal' => $nominal,
                    ];
                }
            }

            // Mengumpulkan detail relation
            if ($req->has('rname_e_relation')) {
                foreach ($req->rname_e_relation as $key => $name) {
                    $relation_e[] = [
                        'name' => $name,
                        'position' => $req->rposition_e_relation[$key],
                        'company' => $req->rcompany_e_relation[$key],
                        'purpose' => $req->rpurpose_e_relation[$key],
                        'relation_type' => array_filter([
                            'Food' => in_array('food', $req->food_e_relation ?? [$key]),
                            'Transport' => in_array('transport', $req->transport_e_relation ?? [$key]),
                            'Accommodation' => in_array('accommodation', $req->accommodation_e_relation ?? [$key]),
                            'Gift' => in_array('gift', $req->gift_e_relation ?? [$key]),
                            'Fund' => in_array('fund', $req->fund_e_relation ?? [$key]),
                        ], fn($checked) => $checked),
                    ];
                }
            }

            // Gabungkan detail entertain dan relation, lalu masukkan ke detail_ca
            $detail_ca = [
                'detail_e' => $detail_e,
                'relation_e' => $relation_e,
            ];
            $model->detail_ca = json_encode($detail_ca);
        }
        $model->total_ca = str_replace('.', '', $req->totalca);
        $model->total_real = "0";
        $model->total_cost = str_replace('.', '', $req->totalca);
        if ($req->input('action_ca_draft')) {
            $model->approval_status = $req->input('action_ca_draft');
        }
        if ($req->input('action_ca_submit')) {
            $model->approval_status = $req->input('action_ca_submit');
        }
        if ($req->input('action_ca_submit')) {
            function findDepartmentHead($employee)
            {
                $manager = Employee::where('employee_id', $employee->manager_l1_id)->first();

                if (!$manager) {
                    return null;
                }

                $designation = Designation::where('job_code', $manager->designation_code)->first();

                if ($designation->dept_head_flag == 'T') {
                    return $manager;
                } else {
                    return findDepartmentHead($manager);
                }
                return null;
            }
            $deptHeadManager = findDepartmentHead($employee_data);

            $managerL1 = $deptHeadManager->employee_id;
            $managerL2 = $deptHeadManager->manager_l1_id;

            $model->status_id = $managerL1;

            $cek_director_id = Employee::select([
                'dsg.department_level2',
                'dsg2.director_flag',
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1) AS department_director"),
                'dsg2.designation_name',
                'dsg2.job_code',
                'emp.fullname',
                'emp.employee_id',
            ])
                ->leftJoin('designations as dsg', 'dsg.job_code', '=', 'employees.designation_code')
                ->leftJoin('designations as dsg2', 'dsg2.department_code', '=', DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1)"))
                ->leftJoin('employees as emp', 'emp.designation_code', '=', 'dsg2.job_code')
                ->where('employees.designation_code', '=', $employee_data->designation_code)
                ->where('dsg2.director_flag', '=', 'T')
                ->get();

            $director_id = "";

            if ($cek_director_id->isNotEmpty()) {
                $director_id = $cek_director_id->first()->employee_id;
            }
            //cek matrix approval
            $total_ca = str_replace('.', '', $req->totalca);
            $data_matrix_approvals = MatrixApproval::where(function ($query) use ($req) {
                if ($req->ca_type === 'dns') {
                    $query->where('modul', 'dns');
                } else {
                    $query->where('modul', 'like', '%' . $req->ca_type . '%');
                }
            })
                ->where('group_company', 'like', '%' . $employee_data->group_company . '%')
                ->where('contribution_level_code', 'like', '%' . $req->companyFilter . '%')
                ->whereRaw(
                    '
            ? BETWEEN
            CAST(SUBSTRING_INDEX(condt, "-", 1) AS UNSIGNED) AND
            CAST(SUBSTRING_INDEX(condt, "-", -1) AS UNSIGNED)',
                    [$total_ca]
                )
                ->get();

            foreach ($data_matrix_approvals as $data_matrix_approval) {

                if ($data_matrix_approval->employee_id == "cek_L1") {
                    $employee_id = $managerL1;
                } else if ($data_matrix_approval->employee_id == "cek_L2") {
                    $employee_id = $managerL2;
                } else if ($data_matrix_approval->employee_id == "cek_director") {
                    $employee_id = $director_id;
                } else {
                    $employee_id = $data_matrix_approval->employee_id;
                }

                $model_approval = new ca_approval;
                $model_approval->ca_id = $req->no_id;
                $model_approval->role_name = $data_matrix_approval->desc;
                $model_approval->employee_id = $employee_id;
                $model_approval->layer = $data_matrix_approval->layer;
                $model_approval->approval_status = 'Pending';

                // Simpan data ke database
                $model_approval->save();
            }
        }
        $model->created_by = $userId;
        $model->save();

        Alert::success('Success Update');
        return redirect()->intended(route('cashadvanced', absolute: false));
    }
    if ($request->ca === 'Ya') {
        $ca = new CATransaction();

        $currentYear = date('Y');
        $currentYearShort = date('y');
        $prefix = 'CA';

        $lastTransaction = CATransaction::whereYear('created_at', $currentYear)
            ->orderBy('no_ca', 'desc')
            ->first();

        if ($lastTransaction && preg_match('/CA' . $currentYearShort . '(\d{6})/', $lastTransaction->no_ca, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        $newNoCa = "$prefix$currentYearShort$newNumber";

        $ca->id = (string) Str::uuid();
        $ca->type_ca = 'dns';
        $ca->no_ca = $newNoCa;
        $ca->no_sppd = $noSppd;
        $ca->user_id = $userId;
        $ca->unit = $request->divisi;

        $ca->contribution_level_code = $request->bb_perusahaan;
        $ca->destination = $request->tujuan;
        $ca->others_location = $request->others_location;

        $ca->ca_needs = $request->keperluan;
        $ca->start_date = $request->mulai;
        $ca->end_date = $request->kembali;

        $ca->date_required = Carbon::parse($request->kembali)->addDays(3);
        $ca->total_days = Carbon::parse($request->mulai)->diffInDays(Carbon::parse($request->kembali));
        $ca->detail_ca = $request->detail_ca;
        $ca->total_ca = (int) str_replace('.', '', $request->totalca);  // Convert to integer
        $ca->total_real = '0';
        $ca->total_cost = (int) str_replace('.', '', $request->totalca);

        $ca->approval_status = $request->status;
        $ca->approval_sett = $request->approval_sett;
        $ca->approval_extend = $request->approval_extend;
        $ca->created_by = $userId;

        $detail_perdiem = [];
        $detail_transport = [];
        $detail_penginapan = [];
        $detail_lainnya = [];

        if ($request->has('start_bt_perdiem')) {
            // $totalPerdiem = str_replace('.', '', $request->total_bt_perdiem[]);
            foreach ($request->start_bt_perdiem as $key => $startDate) {
                $endDate = $request->end_bt_perdiem[$key];
                $totalDays = $request->total_days_bt_perdiem[$key];
                $location = $request->location_bt_perdiem[$key];
                $other_location = $request->other_location_bt_perdiem[$key];
                $companyCode = $request->company_bt_perdiem[$key];
                $nominal = str_replace('.', '', $request->nominal_bt_perdiem[$key]);
                // $totalPerdiem = str_replace('.', '', $request->total_bt_perdiem[]);

                $for_perdiem[] = [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_days' => $totalDays,
                    'location' => $location,
                    'other_location' => $other_location,
                    'company_code' => $companyCode,
                    'nominal' => $nominal,
                    'nominal' => $nominal,
                ];
            }
        }

        // Loop untuk Transport
        if ($request->has('tanggal_bt_transport')) {
            foreach ($request->tanggal_bt_transport as $key => $tanggal) {
                $keterangan = $request->keterangan_bt_transport[$key];
                $companyCode = $request->company_bt_transport[$key];
                $nominal = str_replace('.', '', $request->nominal_bt_transport[$key]);

                $detail_transport[] = [
                    'tanggal' => $tanggal,
                    'keterangan' => $keterangan,
                    'company_code' => $companyCode,
                    'nominal' => $nominal,
                ];
            }
        }

        // Loop untuk Penginapan
        if ($request->has('start_bt_penginapan')) {
            foreach ($request->start_bt_penginapan as $key => $startDate) {
                $endDate = $request->end_bt_penginapan[$key];
                $totalDays = $request->total_days_bt_penginapan[$key];
                $hotelName = $request->hotel_name_bt_penginapan[$key];
                $companyCode = $request->company_bt_penginapan[$key];
                $nominal = str_replace('.', '', $request->nominal_bt_penginapan[$key]);
                $totalPenginapan = str_replace('.', '', $request->total_bt_penginapan[$key]);

                $detail_penginapan[] = [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_days' => $totalDays,
                    'hotel_name' => $hotelName,
                    'company_code' => $companyCode,
                    'nominal' => $nominal,
                    'totalPenginapan' => $totalPenginapan,
                ];
            }
        }

        // Loop untuk Lainnya
        if ($request->has('tanggal_bt_lainnya')) {
            foreach ($request->tanggal_bt_lainnya as $key => $tanggal) {
                $keterangan = $request->keterangan_bt_lainnya[$key];
                $nominal = str_replace('.', '', $request->nominal_bt_lainnya[$key]);
                $totalLainnya = str_replace('.', '', $request->total_bt_lainnya[$key]);

                $detail_lainnya[] = [
                    'tanggal' => $tanggal,
                    'keterangan' => $keterangan,
                    'nominal' => $nominal,
                    'totalLainnya' => $totalLainnya,
                ];
            }
        }

        // Konversi array menjadi JSON untuk disimpan di database
        $detail_ca = [
            'detail_perdiem' => $detail_perdiem,
            'detail_transport' => $detail_transport,
            'detail_penginapan' => $detail_penginapan,
            'detail_lainnya' => $detail_lainnya,
        ];

        $ca->detail_ca = json_encode($detail_ca);

        $ca->save();
    }
    if ($request->ca === 'Ya') {
        $ca = new CATransaction();

        $currentYear = date('Y');
        $currentYearShort = date('y');
        $prefix = 'CA';

        $lastTransaction = CATransaction::whereYear('created_at', $currentYear)
            ->orderBy('no_ca', 'desc')
            ->first();

        if ($lastTransaction && preg_match('/CA' . $currentYearShort . '(\d{6})/', $lastTransaction->no_ca, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        $newNoCa = "$prefix$currentYearShort$newNumber";

        $ca->id = (string) Str::uuid();
        $ca->type_ca = 'dns';
        $ca->no_ca = $newNoCa;
        $ca->no_sppd = $noSppd;
        $ca->user_id = $userId;
        $ca->unit = $request->divisi;

        $ca->contribution_level_code = $request->bb_perusahaan;
        $ca->destination = $request->tujuan;
        $ca->others_location = $request->others_location;

        $ca->ca_needs = $request->keperluan;
        $ca->start_date = $request->mulai;
        $ca->end_date = $request->kembali;

        $ca->date_required = Carbon::parse($request->kembali)->addDays(3);
        $ca->total_days = Carbon::parse($request->mulai)->diffInDays(Carbon::parse($request->kembali));
        $ca->detail_ca = $request->detail_ca;
        $ca->total_ca = (int) str_replace('.', '', $request->totalca);  // Convert to integer
        $ca->total_real = '0';
        $ca->total_cost = (int) str_replace('.', '', $request->totalca);

        $ca->approval_status = $request->status;
        $ca->approval_sett = $request->approval_sett;
        $ca->approval_extend = $request->approval_extend;
        $ca->created_by = $userId;

        $detail_perdiem = [];
        $detail_transport = [];
        $detail_penginapan = [];
        $detail_lainnya = [];

        if ($request->has('start_bt_perdiem')) {
            // $totalPerdiem = str_replace('.', '', $request->total_bt_perdiem[]);
            foreach ($request->start_bt_perdiem as $key => $startDate) {
                $endDate = $request->end_bt_perdiem[$key];
                $totalDays = $request->total_days_bt_perdiem[$key];
                $location = $request->location_bt_perdiem[$key];
                $other_location = $request->other_location_bt_perdiem[$key];
                $companyCode = $request->company_bt_perdiem[$key];
                $nominal = str_replace('.', '', $request->nominal_bt_perdiem[$key]);
                // $totalPerdiem = str_replace('.', '', $request->total_bt_perdiem[]);

                $for_perdiem[] = [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_days' => $totalDays,
                    'location' => $location,
                    'other_location' => $other_location,
                    'company_code' => $companyCode,
                    'nominal' => $nominal,
                    'nominal' => $nominal,
                ];
            }
        }

        // Loop untuk Transport
        if ($request->has('tanggal_bt_transport')) {
            foreach ($request->tanggal_bt_transport as $key => $tanggal) {
                $keterangan = $request->keterangan_bt_transport[$key];
                $companyCode = $request->company_bt_transport[$key];
                $nominal = str_replace('.', '', $request->nominal_bt_transport[$key]);

                $detail_transport[] = [
                    'tanggal' => $tanggal,
                    'keterangan' => $keterangan,
                    'company_code' => $companyCode,
                    'nominal' => $nominal,
                ];
            }
        }

        // Loop untuk Penginapan
        if ($request->has('start_bt_penginapan')) {
            foreach ($request->start_bt_penginapan as $key => $startDate) {
                $endDate = $request->end_bt_penginapan[$key];
                $totalDays = $request->total_days_bt_penginapan[$key];
                $hotelName = $request->hotel_name_bt_penginapan[$key];
                $companyCode = $request->company_bt_penginapan[$key];
                $nominal = str_replace('.', '', $request->nominal_bt_penginapan[$key]);
                $totalPenginapan = str_replace('.', '', $request->total_bt_penginapan[$key]);

                $detail_penginapan[] = [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_days' => $totalDays,
                    'hotel_name' => $hotelName,
                    'company_code' => $companyCode,
                    'nominal' => $nominal,
                    'totalPenginapan' => $totalPenginapan,
                ];
            }
        }

        // Loop untuk Lainnya
        if ($request->has('tanggal_bt_lainnya')) {
            foreach ($request->tanggal_bt_lainnya as $key => $tanggal) {
                $keterangan = $request->keterangan_bt_lainnya[$key];
                $nominal = str_replace('.', '', $request->nominal_bt_lainnya[$key]);
                $totalLainnya = str_replace('.', '', $request->total_bt_lainnya[$key]);

                $detail_lainnya[] = [
                    'tanggal' => $tanggal,
                    'keterangan' => $keterangan,
                    'nominal' => $nominal,
                    'totalLainnya' => $totalLainnya,
                ];
            }
        }

        // Konversi array menjadi JSON untuk disimpan di database
        $detail_ca = [
            'detail_perdiem' => $detail_perdiem,
            'detail_transport' => $detail_transport,
            'detail_penginapan' => $detail_penginapan,
            'detail_lainnya' => $detail_lainnya,
        ];

        $ca->detail_ca = json_encode($detail_ca);

        $ca->save();
    }
