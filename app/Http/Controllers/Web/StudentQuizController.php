<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentQuizController extends Controller
{
    /**
     * Display available quizzes for students.
     */
    public function index(): View
    {
        $quizzes = Quiz::with('learningModule')
            ->withCount('questions')
            ->where('status', 'published')
            ->latest()
            ->get();

        $userAttempts = QuizAttempt::where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->get()
            ->groupBy('quiz_id');

        return view('pages.mahasiswa.quizzes.index', compact('quizzes', 'userAttempts'));
    }

    /**
     * Start or resume a quiz attempt.
     */
    public function start(Quiz $quiz): RedirectResponse
    {
        if ($quiz->status !== 'published') {
            return redirect()->route('mahasiswa.quizzes.index')->with('error', 'Kuis evaluasi belum tersedia untuk publik.');
        }

        if ($quiz->questions()->count() === 0) {
            return redirect()->route('mahasiswa.quizzes.index')->with('error', 'Kuis ini belum memiliki bank soal.');
        }

        // Check for active (in-progress) attempt
        $attempt = QuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->first();

        if (!$attempt) {
            $attempt = QuizAttempt::create([
                'user_id' => auth()->id(),
                'quiz_id' => $quiz->id,
                'started_at' => now(),
                'score' => null,
                'total_correct' => 0,
            ]);
        }

        return redirect()->route('mahasiswa.quizzes.attempt', $attempt);
    }

    /**
     * Display the quiz taking interface with timer and questions.
     */
    public function attempt(QuizAttempt $attempt): View|RedirectResponse
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak. Percobaan kuis ini milik mahasiswa lain.');
        }

        if ($attempt->completed_at !== null) {
            return redirect()->route('mahasiswa.quizzes.result', $attempt);
        }

        $attempt->load(['quiz.learningModule', 'quiz.questions.options']);

        return view('pages.mahasiswa.quizzes.take', compact('attempt'));
    }

    /**
     * Submit answers, calculate score, and mark attempt completed.
     */
    public function submit(Request $request, QuizAttempt $attempt): RedirectResponse
    {
        if ($attempt->user_id !== auth()->id() || $attempt->completed_at !== null) {
            return redirect()->route('mahasiswa.quizzes.result', $attempt);
        }

        $attempt->load('quiz.questions.options');
        $answersData = $request->input('answers', []); // [question_id => selected_option_id]

        $totalWeight = 0;
        $earnedWeight = 0;
        $totalCorrect = 0;

        foreach ($attempt->quiz->questions as $question) {
            $totalWeight += $question->score_weight;
            $selectedOptionId = $answersData[$question->id] ?? null;
            $isCorrect = false;

            if ($selectedOptionId && $question->question_type === 'pilihan_ganda') {
                $selectedOption = $question->options->where('id', $selectedOptionId)->first();
                if ($selectedOption && $selectedOption->is_correct) {
                    $isCorrect = true;
                    $earnedWeight += $question->score_weight;
                    $totalCorrect++;
                }
            }

            QuizAnswer::updateOrCreate(
                [
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ],
                [
                    'selected_option_id' => $selectedOptionId ? (int) $selectedOptionId : null,
                    'is_correct' => $isCorrect,
                ]
            );
        }

        $finalScore = $totalWeight > 0 ? round(($earnedWeight / $totalWeight) * 100) : 0;

        $attempt->update([
            'completed_at' => now(),
            'score' => $finalScore,
            'total_correct' => $totalCorrect,
        ]);

        return redirect()->route('mahasiswa.quizzes.result', $attempt)->with('success', 'Evaluasi kuis telah selesai dikerjakan! Berikut rekapitulasi nilai Anda.');
    }

    /**
     * Display the score breakdown, correct/incorrect review, and celebration.
     */
    public function result(QuizAttempt $attempt): View
    {
        if ($attempt->user_id !== auth()->id() && !auth()->user()->isAdmin() && !auth()->user()->isDosen()) {
            abort(403, 'Akses ditolak.');
        }

        if ($attempt->completed_at === null) {
            return redirect()->route('mahasiswa.quizzes.attempt', $attempt);
        }

        $attempt->load(['quiz.learningModule', 'answers.question.options', 'answers.selectedOption']);

        return view('pages.mahasiswa.quizzes.result', compact('attempt'));
    }
}
