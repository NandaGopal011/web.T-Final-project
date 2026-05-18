function placeBid(listingId) {

    let amount = document.getElementById("bidAmount").value;

    let xhr = new XMLHttpRequest();

    xhr.open("POST", "../../api/place_bid.php", true);

    xhr.setRequestHeader(
        "Content-type",
        "application/x-www-form-urlencoded"
    );

    xhr.onload = function() {
        alert(this.responseText);
    }

    xhr.send(
        "listing_id=" + listingId +
        "&amount=" + amount
    );
}
