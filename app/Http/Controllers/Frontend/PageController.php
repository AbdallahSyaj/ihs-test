<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Catalog\DatabaseStorefrontCatalogProvider;
use App\Http\Requests\Frontend\StoreContactRequest;
use App\Http\Requests\Frontend\StoreProductInquiryRequest;
use App\Models\AboutUs;
use App\Models\ContactUs;
use App\Models\ContactUsRequest;
use App\Models\ProductInquiryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        private readonly DatabaseStorefrontCatalogProvider $storefrontCatalog,
    ) {}
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
        $catalog = $this->storefrontCatalog->catalog();

        return view('frontend.products', [
            'categories' => $catalog['categories'],
            'products' => $catalog['products'],
        ]);
    }

    public function show(string $slug): View
    {
        $product = $this->storefrontCatalog->findProduct($slug);

        if ($product === null) {
            abort(404);
        }

        return view('frontend.product-details', [
            'slug' => $slug,
            'product' => $product,
            'productName' => $product['name'],
        ]);
    }

    public function contact(): View
    {
        return view('frontend.contact', [
            'contactUs' => ContactUs::query()->first(),
        ]);
    }

    public function cart(): View
    {
        return view('frontend.cart');
    }

    public function storeProductInquiry(StoreProductInquiryRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        ProductInquiryRequest::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'items' => $data['items'],
        ]);

        $message = 'Thank you — your equipment inquiry has been received. We will contact you shortly.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()->route('frontend.cart')->with('inquiry_success', $message);
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
