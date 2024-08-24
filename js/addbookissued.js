var studentinfo = null;
var course = null;
var bookDetails = null;
var borrowedBooks = [];

const client = new Paho.MQTT.Client("localhost", 9001, "web-client-" + parseInt(Math.random() * 100, 10));
let reconnectAttempts = 0;
const maxReconnectAttempts = 5;

function reconnect() {
    if (reconnectAttempts < maxReconnectAttempts) {
        console.log("Attempting to reconnect to MQTT broker...");
        client.connect({
            onSuccess: function () {
                console.log("Reconnected to MQTT broker");
                reconnectAttempts = 0;
                client.subscribe('library/borrowed_books');
                client.subscribe('library/returned_books');
            },
            onFailure: function (error) {
                console.error("MQTT reconnection error:", error);
                reconnectAttempts++;
                setTimeout(reconnect, 5000);
            }
        });
    } else {
        console.error("Max reconnection attempts reached. Please check the broker.");
    }
}

client.onConnectionLost = function (responseObject) {
    console.log('Connection lost:', responseObject.errorMessage);
    reconnect();
};

client.onMessageArrived = function (message) {
    try {
        const msgs = JSON.parse(message.payloadString);
        console.log(`Received messages: ${JSON.stringify(msgs)}`);

        switch (message.destinationName) {
            case 'library/borrowed_books':
                handleBorrowedBooks(msgs);
                break;
            case 'library/returned_books':
                console.log('Nay g balik na libro');
                handleReturnedBooks(msgs);
                break;
            default:
                console.warn("Unknown topic:", message.destinationName);
        }
    } catch (e) {
        console.error("Error parsing message payload:", e);
    }
};

function handleBorrowedBooks(msgs) {
    if (Array.isArray(msgs)) {
        const validMessages = msgs.filter(msg => msg.student_id && msg.book_id && msg.return_date);

        if (validMessages.length > 0) {
            const booksList = validMessages.map(book =>
                `Student ID: ${book.student_id}, Student Name: ${book.student_name}, Book Title: ${book.book_title}, Book ID: ${book.book_id}, Return Date: ${book.return_date} \n \n`).join('\n');

            Swal.fire({
                title: 'New Books Borrowed!',
                text: booksList,
                icon: 'info',
                confirmButtonText: 'Ok'
            }).then(() => {
                console.log('SweetAlert2 displayed successfully.');
            });
        } else {
            console.error("No valid messages found in array:", msgs);
        }
    } else {
        console.error("Received message is not an array:", msgs);
    }
}

function handleReturnedBooks(msgs) {
    if (Array.isArray(msgs)) {
        const validMessages = msgs.filter(msg => msg.book_id && msg.return_date);

        if (validMessages.length > 0) {
            const booksList = validMessages.map(book =>
                `Book ID: ${book.book_id}, Return Date: ${book.return_date} \n \n`).join('\n');

            Swal.fire({
                title: 'Books Returned!',
                text: booksList,
                icon: 'success',
                confirmButtonText: 'Ok'
            }).then(() => {
                console.log('SweetAlert2 displayed successfully.');
            });
        } else {
            console.error("No valid messages found in array:", msgs);
        }
    } else {
        console.error("Received message is not an array:", msgs);
    }
}

client.connect({
    onSuccess: function () {
        console.log("Connected to MQTT broker");
        client.subscribe('library/borrowed_books');
        client.subscribe('library/returned_books');
    },
    onFailure: function (error) {
        console.error("MQTT connection error:", error);
    }
});

$(".edit").click(function () {
    var request = $.ajax({
        url: "issuedbook.php",
        method: "GET",
        data: { id: this.value },
        dataType: "json",
    });

    request.done(function (msg) {
        studentinfo = msg;
        var courseSplit = msg.course_section.split(' - ');
        course = courseSplit[0];

        $("#id").val(msg.tbl_attendance_id);
        $("#student_name").val(msg.student_name);
        $("#course_m").val(msg.course_section);
        $("#time_in_m").val(msg.time_in);
        $("#time_out_m").val(msg.time_out);
        $("#log_date_m").val(msg.timein_log_date);
    });
});

$("#book").on("change", function () {
    var bookId = this.value;

    if (bookId) {
        $.ajax({
            url: "viewbooksdetails.php",
            method: "GET",
            data: { id: bookId },
            dataType: "json",
        }).done(function (response) {
            bookDetails = response;
        });
    } else {
        bookDetails = null;
    }
});

$("#add-book").on("click", function () {
    var returndate = $("#returndate").val();

    if (!returndate) {
        Swal.fire({
            title: 'Error!',
            text: 'Please select a Book and Return Date.',
            icon: 'error',
            confirmButtonText: 'Ok'
        });
        return;
    }

    var bookEntry = {
        student_name: studentinfo.student_name,
        tbl_attendance_id: studentinfo.tbl_attendance_id,
        tbl_logdate: studentinfo.timein_log_date,
        qrcode: studentinfo.generated_code,
        course: course,
        id: bookDetails.id,
        author: bookDetails.author,
        book_title: bookDetails.book_title,
        publisher: bookDetails.publisher,
        return_date: returndate,
    };

    borrowedBooks.push(bookEntry);

    var newRow = `<tr>
        <td style="display:none">${studentinfo.tbl_attendance_id}</td>
        <td>${bookDetails.id}</td>
        <td>${bookDetails.author}</td>
        <td>${bookDetails.book_title}</td>
        <td>${bookDetails.publisher}</td>
        <td>${returndate}</td>
        <td><button class="btn btn-danger delete-book">Delete</button></td>
    </tr>`;

    $("#book-list tbody").append(newRow);

    bookDetails = null;
    $("#book").val("");
    $("#returndate").val("");
});

$("#book-list").on("click", ".delete-book", function () {
    var row = $(this).closest("tr");
    var bookId = row.find("td:nth-child(2)").text();

    borrowedBooks = borrowedBooks.filter((book) => book.id != bookId);
    row.remove();
});

$("#savebook").click(function () {
    if (borrowedBooks.length === 0) {
        Swal.fire({
            title: 'Error!',
            text: 'No books to save or borrow',
            icon: 'error',
            confirmButtonText: 'Ok'
        });
        return;
    }

    console.log("Data to be sent:", JSON.stringify(borrowedBooks));

    $.ajax({
        url: "insertborrowedbook.php",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(borrowedBooks),
        success: function (response) {
            $("#book-list tbody").empty();

            borrowedBooks.forEach(function (book) {
                const message = JSON.stringify({
                    action: 'borrow',
                    student_id: book.tbl_attendance_id,
                    student_name: book.student_name,
                    book_title: book.book_title,
                    book_id: book.id,
                    return_date: book.return_date
                });
                client.send("library/borrowed_books", message, 0);
            });

            borrowedBooks = [];
        },
    });
});
