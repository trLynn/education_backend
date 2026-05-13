<?php

namespace App\Repositories\Interfaces;

interface NoteRepositoryInterface
{
    public function updateOrCreateNote($studentId, $subChapterId, $content);
    public function getNote($studentId, $subChapterId);
}
