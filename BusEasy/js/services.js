class Services extends HTMLElement {
    constructor() {
        super();
        
        // Create a shadow root
        const shadow = this.attachShadow({ mode: 'open' });

        // Create a template
        // const template = document.createElement('template');
        shadow.innerHTML = `
            <style>
                @import "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css";
                @import "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css";
                @import "styles/style1.css";
            </style>
                <div id="services" class="section text-center ">
                    <div class="container">
                        <h2>Services</h2>
                        <div class="row mt-4">
                            <!-- Service Card 1 -->
                            <div class="col-md-3">
                                <div class="service-card">
                                    <h5>Online Seat Reservation</h5>
                                    <p>Book your seat easily from the comfort of your home.</p>
                                </div>
                            </div>
                            <!-- Service Card 2 -->
                            <div class="col-md-3">
                                <div class="service-card">
                                    <h5>Real-Time Notifications</h5>
                                    <p>Stay updated with the latest travel alerts.</p>
                                </div>
                            </div>
                            <!-- Service Card 3 -->
                            <div class="col-md-3">
                                <div class="service-card">
                                    <h5>Flexible Payment Options</h5>
                                    <p>Pay securely using multiple payment methods.</p>
                                </div>
                            </div>
                            <!-- Service Card 4 -->
                            <div class="col-md-3">
                                <div class="service-card">
                                    <h5>24/7 Customer Support</h5>
                                    <p>We’re here to help you anytime, anywhere.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        `;

        // Attach the template content to the shadow DOM
        // shadow.appendChild(template.content.cloneNode(true));
    }
}

window.customElements.define('services-section', Services);

