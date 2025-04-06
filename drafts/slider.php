<div class="category-slider-container">
    <div class="category-slider">
        <button class="prev-btn"><img src="assets/web_img/left-arrow.png" alt=""></button>

        <div class="slider-wrapper"> <!-- New wrapper for overflow control -->
            <div class="slider">
                <div class="category-card">
                    <div class="category-image"></div>
                    <p class="category-title">FLOWERS</p>
                </div>
                <div class="category-card">
                    <div class="category-image"></div>
                    <p class="category-title">ACCESSORIES</p>
                </div>
                <div class="category-card">
                    <div class="category-image"></div>
                    <p class="category-title">HANDMADE TOYS & DECOR</p>
                </div>
                <div class="category-card">
                    <div class="category-image"></div>
                    <p class="category-title">CLOTHING & WEARABLES</p>
                </div>
                <div class="category-card">
                    <div class="category-image"></div>
                    <p class="category-title">OTHERS</p>
                </div>
            </div>
        </div>

        <button class="next-btn"><img src="assets/web_img/right-arrow.png" alt=""></button>
    </div>
</div>

<style>

.category-slider-container {
    height: 500px;
    width: 70%;
    display: flex;

}

.category-slider {
    height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    width: 100%;
    margin: auto;
}

.slider-wrapper {
    border: 1px solid red;      
    height: 100%;
    width: 80%;
    overflow: hidden;
    position: relative;
    display: flex;
    align-items: center; /* Centers vertically */
}

.slider {
    display: flex;
    gap: 50px;
    transition: transform 0.5s ease-in-out;
    min-width: 100%;
    white-space: nowrap; /* Prevents wrapping */
}


.category-card {
    flex: 0 0 auto; /* Prevents shrinking */
    width: 300px;
    height: 400px;
    background: white;
    border-radius: 10px;
    box-shadow: 0px 2px 14px 2px rgba(0, 0, 0, 0.189); 
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    margin-left: 10px;
}

.category-image {
    width: 250px;
    height: 250px;
    border: 1px solid lightgray;
    margin-bottom: 50px;
}

.category-title {
    font-size: 16px;
    font-weight: 800;
    color: #2B202E;
    text-shadow: 0px 1px 10px rgba(0, 0, 0, 0.244);
}

/* BUTTONS */
.prev-btn, .next-btn {
    border: none;
    background-color: white;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0px 1px 4px 2px rgba(0, 0, 0, 0.189); 
    transition: 0.3s;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2; /* Ensures buttons stay above */
}

.prev-btn { left: 10px; }
.next-btn { right: 10px; }

.prev-btn img, .next-btn img {
    width: 25px;
    height: 25px;
}

.prev-btn:hover, .next-btn:hover {
    transform: translateY(-50%) scale(1.1);
}



</style>