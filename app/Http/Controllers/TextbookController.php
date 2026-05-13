<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreTextbookRequest;
use App\Http\Resources\TextbookResource;
use App\Services\TextbookService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TextbookController extends Controller {
    use ApiResponse;

    protected $service;

    public function __construct(TextbookService $service) {
        $this->service = $service;
    }

    public function index() {
        $books = $this->service->getAllBooks();
        return $this->successResponse(TextbookResource::collection($books), "စာအုပ်များ ရရှိပါပြီ");
    }

    public function store(StoreTextbookRequest $request) {
        try {
            $book = $this->service->createBook($request->validated());
            return $this->successResponse(new TextbookResource($book), "ဖန်တီးမှု အောင်မြင်ပါသည်", 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function show($id) {
        try {
            $book = $this->service->getBook($id);
            return $this->successResponse(new TextbookResource($book), "အောင်မြင်ပါသည်");
        } catch (\Exception $e) {
            return $this->errorResponse("စာအုပ်ရှာမတွေ့ပါ", 404);
        }
    }

    public function update(Request $request, $id) {
        $book = $this->service->updateBook($id, $request->all());
        return $this->successResponse(new TextbookResource($book), "ပြင်ဆင်မှု အောင်မြင်ပါသည်");
    }

    public function destroy($id) {
        $this->service->deleteBook($id);
        return $this->successResponse(null, "ဖျက်သိမ်းပြီးပါပြီ");
    }
}