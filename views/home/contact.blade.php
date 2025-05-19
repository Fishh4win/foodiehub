@extends("layouts.main")

@section("title", $title)

@section("content")
<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="text-center mb-4">Contact Us</h1>
                    
                    <div class="row mb-5">
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <div class="bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                            </div>
                            <h5 class="mt-3">Address</h5>
                            <p>123 Food Street<br>Cuisine City, PH 12345</p>
                        </div>
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <div class="bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-phone fa-2x text-primary"></i>
                            </div>
                            <h5 class="mt-3">Phone</h5>
                            <p>(123) 456-7890<br>(098) 765-4321</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-envelope fa-2x text-primary"></i>
                            </div>
                            <h5 class="mt-3">Email</h5>
                            <p>info@foodiehub.com<br>support@foodiehub.com</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h2>Send Us a Message</h2>
                        <p>Have questions, suggestions, or feedback? Fill out the form below and we'll get back to you as soon as possible.</p>
                        
                        <form action="/contact/send" method="POST" class="mt-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Your Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="mb-4">
                        <h2>Frequently Asked Questions</h2>
                        <div class="accordion mt-3" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                        How do I place an order?
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        To place an order, browse our food selection, add items to your cart, and proceed to checkout. You'll need to be logged in to complete your purchase.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        How can I become a vendor?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        To become a vendor, click on "Become a Vendor" in the navigation menu and complete the registration process. Once approved, you can start listing your food products.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        What payment methods do you accept?
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        We currently accept cash on delivery. We're working on adding more payment options in the near future.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
