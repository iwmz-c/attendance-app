<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Carbon\Carbon;

class StoreStampCorrectionRequest extends FormRequest
{
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
            'work_date' => ['required', 'date'],
            'attendance_id' => ['nullable', 'integer'],

            // 時刻はフォームが空でも送れるよう nullable
            'requested_clock_in_at' => ['nullable', 'date_format:H:i'],
            'requested_clock_out_at' => ['nullable', 'date_format:H:i'],

            // 要件④
            'requested_note' => ['required', 'string'],

            // breaks[0][start], breaks[0][end]... を受ける
            'breaks' => ['nullable', 'array'],
            'breaks.*.start' => ['nullable', 'date_format:H:i'],
            'breaks.*.end' => ['nullable', 'date_format:H:i'],
        ];
    }

    public function messages(): array
    {
        return [
            // 要件④
            'requested_note.required' => '備考を記入してください',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $workDate = $this->input('work_date');
            if (!$workDate) return;

            // work_date + 時刻 を datetime に変換するヘルパ
            $toDateTime = function (?string $time) use ($workDate): ?Carbon {
                if (!$time) return null;
                return Carbon::createFromFormat('Y-m-d H:i', $workDate . ' ' . $time);
            };

            $in  = $toDateTime($this->input('requested_clock_in_at'));
            $out = $toDateTime($this->input('requested_clock_out_at'));

            // ① 出勤 > 退勤（または 退勤 < 出勤）
            if ($in && $out && $in->gte($out)) {
                $validator->errors()->add(
                    'requested_clock_in_at',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            $breaks = $this->input('breaks', []);
            if (!is_array($breaks)) $breaks = [];

            foreach ($breaks as $i => $b) {
                $start = $toDateTime($b['start'] ?? null);
                $end   = $toDateTime($b['end'] ?? null);

                // 両方空ならスキップ
                if (!$start && !$end) continue;

                // 片方だけ入力（要件には無いけど、ここは不適切として同じメッセージに寄せる）
                if (!$start || !$end) {
                    $validator->errors()->add("breaks.$i.start", '休憩時間が不適切な値です');
                    continue;
                }

                // 休憩開始 > 休憩終了（これも不適切として同じメッセージ）
                if ($end->lte($start)) {
                    $validator->errors()->add("breaks.$i.start", '休憩時間が不適切な値です');
                }

                // ② 休憩開始が出勤より前 / 退勤より後
                if ($in && $start->lt($in)) {
                    $validator->errors()->add("breaks.$i.start", '休憩時間が不適切な値です');
                }
                if ($out && $start->gt($out)) {
                    $validator->errors()->add("breaks.$i.start", '休憩時間が不適切な値です');
                }

                // ③ 休憩終了が退勤より後
                if ($out && $end->gt($out)) {
                    $validator->errors()->add(
                        "breaks.$i.end",
                        '休憩時間もしくは退勤時間が不適切な値です'
                    );
                }
            }
        });
    }
}
