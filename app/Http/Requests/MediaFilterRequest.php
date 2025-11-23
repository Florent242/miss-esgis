<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'photo' => 'image|mimes:jpg,jpeg,png,gif|max:10240', // 10 MB max
            'video' => 'mimes:mp4,avi,mov,webm|max:51200' // 50 MB max
        ];
    }

    public function messages(): array
    {
        return [
            'photo.max' => 'La photo ne doit pas dépasser 10 MB.',
            'photo.mimes' => 'La photo doit être au format JPG, JPEG, PNG ou GIF.',
            'video.max' => 'La vidéo ne doit pas dépasser 50 MB. Veuillez compresser votre vidéo.',
            'video.mimes' => 'La vidéo doit être au format MP4, AVI, MOV ou WEBM.',
        ];
    }
}
