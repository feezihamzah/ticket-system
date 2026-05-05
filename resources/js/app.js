let selectedSeats = [];
let expiresAt = null;
let reservationId = null;

// load seats
window.loadSeats = function () {
    document.getElementById("eventArea").style.display = "block";

    const eventSelect = document.getElementById("eventSelect");
    const eventId = eventSelect.value;

    if (!eventId) {
        document.getElementById("bookingArea").style.display = "none";
        document.getElementById("summaryArea").style.display = "none";
        return;
    }

    document.getElementById("bookingArea").style.display = "block";
    document.getElementById("summaryArea").style.display = "block";

    // ONLY reset kalau belum reserve
    if (!reservationId) {
        selectedSeats = [];
        document.getElementById("selectedSeatsText").innerText = "-";
    }

    document.getElementById("timer").innerText = "-";

    // button control
    if (reservationId) {
        document.getElementById("reserveBtn").style.display = "none";
        document.getElementById("confirmBtn").style.display = "block";
    } else {
        document.getElementById("reserveBtn").style.display = "block";
        document.getElementById("confirmBtn").style.display = "none";
    }

    document.getElementById("reserveBtn").disabled = selectedSeats.length === 0;

    const eventName = eventSelect.options[eventSelect.selectedIndex].text;
    document.getElementById("eventName").innerText = eventName;

    fetch(`/events/${eventId}/seats`)
        .then((res) => res.json())
        .then((seats) => {
            const container = document.getElementById("seats");
            container.innerHTML = "";

            const grouped = {};

            seats.forEach((seat) => {
                const row = seat.seat_number.charAt(0);
                if (!grouped[row]) grouped[row] = [];
                grouped[row].push(seat);
            });

            Object.keys(grouped).forEach((row) => {
                const rowDiv = document.createElement("div");
                rowDiv.className = "row";

                const label = document.createElement("div");
                label.className = "row-label";
                label.innerText = row;

                rowDiv.appendChild(label);

                const rowSeats = grouped[row];

                const leftGroup = document.createElement("div");
                leftGroup.className = "seat-group";

                const rightGroup = document.createElement("div");
                rightGroup.className = "seat-group";

                rowSeats.forEach((seat, index) => {
                    const div = document.createElement("div");

                    div.innerText = seat.seat_number;

                    const status = (seat.status || "").toLowerCase();
                    div.className = "seat " + status;

                    if (selectedSeats.includes(seat.seat_number)) {
                        div.classList.add("selected");
                    }

                    if (status === "available" && !reservationId) {
                        div.onclick = () =>
                            window.toggleSeat(div, seat.seat_number);
                    }

                    if (index < rowSeats.length / 2) {
                        leftGroup.appendChild(div);
                    } else {
                        rightGroup.appendChild(div);
                    }
                });

                const aisle = document.createElement("div");
                aisle.className = "aisle";

                rowDiv.appendChild(leftGroup);
                rowDiv.appendChild(aisle);
                rowDiv.appendChild(rightGroup);

                container.appendChild(rowDiv);
            });
        });
};

// select seat
window.toggleSeat = function (el, seatNumber) {
    if (reservationId) return;

    if (selectedSeats.includes(seatNumber)) {
        selectedSeats = selectedSeats.filter((s) => s !== seatNumber);
        el.classList.remove("selected");
    } else {
        selectedSeats.push(seatNumber);
        el.classList.add("selected");
    }

    document.getElementById("selectedSeatsText").innerText =
        selectedSeats.length ? selectedSeats.join(", ") : "-";

    document.getElementById("reserveBtn").disabled = selectedSeats.length === 0;
};

// reserve
window.reserveSeats = function () {
    const eventId = document.getElementById("eventSelect").value;

    if (!eventId || selectedSeats.length === 0) {
        alert("Select event & seat first");
        return;
    }

    fetch(`/events/${eventId}/reserve`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify({ seats: selectedSeats }),
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.message) {
                alert(data.message);
                return;
            }

            reservationId = data.reservation_id;
            expiresAt = new Date(data.expires_at);

            alert("Seats reserved!");

            document.getElementById("confirmBtn").style.display = "block";
            document.getElementById("reserveBtn").style.display = "none";
            document.getElementById("cancelBtn").style.display = "block";

            startCountdown();
        });
};

// cancel
window.cancelReservation = function () {
    if (!reservationId) return;

    fetch(`/reservations/${reservationId}/cancel`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    })
        .then((res) => res.json())
        .then((data) => {
            alert(data.message || "Reservation cancelled");

            // reset state
            reservationId = null;
            selectedSeats = [];

            document.getElementById("confirmBtn").style.display = "none";
            document.getElementById("cancelBtn").style.display = "none";
            document.getElementById("reserveBtn").style.display = "block";

            document.getElementById("selectedSeatsText").innerText = "-";
            document.getElementById("timer").innerText = "-";

            loadSeats();
        });
};

// confirm
window.confirmBooking = function () {
    if (!reservationId) {
        alert("Please reserve first!");
        return;
    }

    fetch(`/reservations/${reservationId}/confirm`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    })
        .then((res) => res.json())
        .then((data) => {
            alert(data.message || "Booking confirmed!");

            // hide booking UI
            document.getElementById("eventArea").style.display = "none";
            document.getElementById("bookingArea").style.display = "none";
            document.getElementById("summaryArea").style.display = "none";

            // show success page
            document.getElementById("successArea").style.display = "block";

            // show details
            document.getElementById("successEvent").innerText =
                "Event: " + document.getElementById("eventName").innerText;

            document.getElementById("successSeats").innerText =
                "Seats: " + selectedSeats.join(", ");
        });
};

// countdown
function startCountdown() {
    const timerEl = document.getElementById("timer");

    const interval = setInterval(() => {
        const diff = expiresAt - new Date();

        if (diff <= 0) {
            clearInterval(interval);

            timerEl.innerText = "Expired!";

            document.getElementById("confirmBtn").style.display = "none";
            document.getElementById("reserveBtn").style.display = "block";

            reservationId = null;
            selectedSeats = [];

            alert("Reservation expired");

            loadSeats();
            return;
        }

        const m = Math.floor(diff / 60000);
        const s = Math.floor((diff % 60000) / 1000);

        timerEl.innerText = `Time left: ${m}:${s < 10 ? "0" : ""}${s}`;
    }, 1000);
}

window.goBack = function () {
    location.reload();
};
