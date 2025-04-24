class AboutUs extends HTMLElement {
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
                .section {
                    background-color: #000000;
                    color: white;
                }
            </style>
            <div class="section" id="about">
                <div class="container">
                    <h2>About Us</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <img src="img/about-us.png" alt="About Us" class="img-fluid rounded shadow">
                        </div>
                        <div class="col-md-6 text-md-start mt-4">
                            <p>
                                Bus Easy is committed to making your travel experience seamless and comfortable. 
                                With real-time updates and easy reservations, we redefine bus travel for the modern passenger. 
                                Join us for a journey that's stress-free and reliable. <br>
                                We strive to provide unmatched customer service, keeping you informed every step of the way.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Attach the template content to the shadow DOM
        // shadow.appendChild(template.content.cloneNode(true));
    }
}

// Define the new element
customElements.define('about-us', AboutUs);