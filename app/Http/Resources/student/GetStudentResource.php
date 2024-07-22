<?php

namespace App\Http\Resources\student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class   GetStudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => ucwords($this->name),
            'email' => $this->email,
            'student_id' => $this->student_id,
            'stautus' => $this->status,
            'date_of_birth' => ($this->date_of_birth) ? date('d-m-Y', strtotime($this->date_of_birth)) : null,
            'has_card' => $this->studenCard ? 'yes' : 'no'
        ];
    }
}
