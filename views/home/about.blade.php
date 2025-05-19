@extends("layouts.main")

@section("title", $title)

@section("content")
<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="text-center mb-4">About FoodieHub</h1>
                    
                    <div class="mb-4">
                        <h2>Our Mission</h2>
                        <p>At FoodieHub, our mission is to connect food lovers with local vendors, creating a vibrant marketplace where delicious food is just a few clicks away. We believe that everyone deserves access to quality food from trusted local sources.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>Who We Are</h2>
                        <p>FoodieHub is a premier food marketplace platform established in 2023. We provide a seamless connection between food vendors and customers, making it easy to discover, order, and enjoy a wide variety of culinary delights.</p>
                        <p>Our team is passionate about food and technology, and we're committed to creating an exceptional experience for both vendors and customers.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>What We Offer</h2>
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-utensils fa-2x text-primary me-3"></i>
                                    </div>
                                    <div>
                                        <h5>Diverse Food Selection</h5>
                                        <p>Browse through a wide range of food categories from various local vendors.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-store fa-2x text-primary me-3"></i>
                                    </div>
                                    <div>
                                        <h5>Vendor Opportunities</h5>
                                        <p>We provide a platform for food vendors to showcase their products and grow their business.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-star fa-2x text-primary me-3"></i>
                                    </div>
                                    <div>
                                        <h5>Ratings & Reviews</h5>
                                        <p>Make informed choices with our transparent rating and review system.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-truck fa-2x text-primary me-3"></i>
                                    </div>
                                    <div>
                                        <h5>Convenient Delivery</h5>
                                        <p>Enjoy your favorite food delivered right to your doorstep.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h2>Our Values</h2>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>Quality:</strong> We are committed to maintaining high standards for all vendors on our platform.
                            </li>
                            <li class="list-group-item">
                                <strong>Community:</strong> We support local food vendors and help them connect with customers in their area.
                            </li>
                            <li class="list-group-item">
                                <strong>Innovation:</strong> We continuously improve our platform to provide the best experience for all users.
                            </li>
                            <li class="list-group-item">
                                <strong>Transparency:</strong> We believe in honest reviews and clear information about all products.
                            </li>
                            <li class="list-group-item">
                                <strong>Customer Satisfaction:</strong> We prioritize the needs and preferences of our customers.
                            </li>
                        </ul>
                    </div>
                    
                    <div class="text-center mt-5">
                        <h3>Join Our Community</h3>
                        <p>Whether you're a food lover looking for your next delicious meal or a vendor wanting to expand your reach, FoodieHub is the place for you.</p>
                        <div class="mt-4">
                            <a href="/register" class="btn btn-primary me-2">Sign Up as Customer</a>
                            <a href="/vendor/register" class="btn btn-outline-primary">Become a Vendor</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
