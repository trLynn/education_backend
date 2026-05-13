<?php
namespace App\Repositories\Eloquent;

use App\Models\Textbook;
use App\Repositories\Interfaces\TextbookRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class TextbookRepository implements TextbookRepositoryInterface {
    public function all() {
        return Textbook::where('author_id', Auth::id())->get();
    }

    public function findByTitle($title) {
        return Textbook::where('title', $title)->first();
    }

    public function countByAuthor($authorId) {
        return Textbook::where('author_id', $authorId)->count();
    }

    public function create(array $data) {
        return Textbook::create($data);
    }

    public function findById($id) {
        return Textbook::with('chapters.subChapters.blocks')->findOrFail($id);
    }

    public function update($id, array $data) {
        $record = Textbook::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete($id) {
        $record = Textbook::findOrFail($id);
        return $record->delete();
    }

    public function getPublished() {
        return Textbook::where('is_published', true)->get();
    }

    public function findWithStudentNotes($textbookId, $studentId) {
        return Textbook::with([
            'chapters.subChapters.blocks',
            'chapters.subChapters.studentNotes' => function($query) use ($studentId) {
                $query->where('student_id', $studentId);
            }
        ])->findOrFail($textbookId);
    }
}