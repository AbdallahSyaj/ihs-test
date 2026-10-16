<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreContactRequest;
use App\Models\AboutUs;
use App\Models\ContactUs;
use App\Models\ContactUsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('frontend.home');
    }

    public function about(): View
    {
        return view('frontend.about', [
            'aboutUs' => AboutUs::query()->first(),
        ]);
    }

    public function products(): View
    {
        return view('frontend.products');
    }

    public function show(string $slug): View
    {
        $product = $this->catalogProduct($slug);

        if ($product === null) {
            abort(404);
        }

        return view('frontend.product-details', [
            'slug' => $slug,
            'productName' => $product['name'],
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function catalogProduct(string $slug): ?array
    {
        $path = public_path('frontend/data/products.json');
        if (! is_file($path)) {
            return null;
        }

        $catalog = json_decode((string) file_get_contents($path), true);
        if (! is_array($catalog) || ! is_array($catalog['products'] ?? null)) {
            return null;
        }

        foreach ($catalog['products'] as $product) {
            if (! is_array($product) || ($product['id'] ?? null) !== $slug) {
                continue;
            }

            if (! is_string($product['name'] ?? null) || $product['name'] === '') {
                return null;
            }

            return $product;
        }

        return null;
    }

    public function contact(): View
    {
        return view('frontend.contact', [
            'contactUs' => ContactUs::query()->first(),
        ]);
    }

    public function store(StoreContactRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        ContactUsRequest::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'],
            'message' => $data['message'],
        ]);

        $message = "Thank you — we'll be in touch shortly.";

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()->route('frontend.contact')->with('contact_success', $message);
    }
}
