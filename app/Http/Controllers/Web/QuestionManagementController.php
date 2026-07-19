<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionManagementController extends Controller
{
    /**
     * Display the Bank Soal & Kunci Jawaban interface for a quiz.
     */
    public function index(Quiz $quiz): View
    {
        $quiz->load(['learningModule', 'questions.options']);

        return view('pages.manage.quizzes.questions.index', compact('quiz'));
    }

    /**
     * Store a new question along with its multiple choice options and correct answer flag.
     */
    public function store(Request $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'question_type' => ['required', 'in:pilihan_ganda,esai'],
            'score_weight' => ['required', 'integer', 'min:1', 'max:100'],
            'options' => ['nullable', 'array'],
            'options.*.text' => ['required_with:options', 'string'],
            'correct_option_index' => ['nullable', 'integer'],
        ]);

        $question = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'score_weight' => $validated['score_weight'],
        ]);

        if ($validated['question_type'] === 'pilihan_ganda' && $request->has('options')) {
            $correctIndex = (int) $request->input('correct_option_index', 0);
            
            foreach ($request->input('options') as $index => $optData) {
                if (!empty($optData['text'])) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => trim($optData['text']),
                        'is_correct' => ($index === $correctIndex),
                    ]);
                }
            }
        }

        return redirect()->route('manage.quizzes.questions.index', $quiz)->with('success', 'Soal evaluasi dan kunci jawaban berhasil ditambahkan ke bank soal!');
    }

    /**
     * Update an existing question and its options.
     */
    public function update(Request $request, Quiz $quiz, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'question_type' => ['required', 'in:pilihan_ganda,esai'],
            'score_weight' => ['required', 'integer', 'min:1', 'max:100'],
            'options' => ['nullable', 'array'],
            'options.*.text' => ['required_with:options', 'string'],
            'correct_option_index' => ['nullable', 'integer'],
        ]);

        $question->update([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'score_weight' => $validated['score_weight'],
        ]);

        if ($validated['question_type'] === 'pilihan_ganda' && $request->has('options')) {
            $question->options()->delete();
            $correctIndex = (int) $request->input('correct_option_index', 0);

            foreach ($request->input('options') as $index => $optData) {
                if (!empty($optData['text'])) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => trim($optData['text']),
                        'is_correct' => ($index === $correctIndex),
                    ]);
                }
            }
        } else {
            $question->options()->delete();
        }

        return redirect()->route('manage.quizzes.questions.index', $quiz)->with('success', 'Soal evaluasi berhasil diperbarui!');
    }

    /**
     * Remove the specified question from the quiz.
     */
    public function destroy(Quiz $quiz, Question $question): RedirectResponse
    {
        $question->delete();

        return redirect()->route('manage.quizzes.questions.index', $quiz)->with('success', 'Soal evaluasi telah berhasil dihapus dari bank soal.');
    }
}
