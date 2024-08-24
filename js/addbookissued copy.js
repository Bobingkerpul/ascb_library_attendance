var studentinfo = null;
var course = null;
var bookDetails = null;
var borrowedBooks = [];

// // Initialize MQTT client with unique ID
// const client = new Paho.MQTT.Client('localhost', 9001, 'browser-client-addbookissued');
// let reconnectAttempts = 0;
// const maxReconnectAttempts = 5;

// // Function to handle reconnection
// function reconnect() {
//     if (reconnectAttempts < maxReconnectAttempts) {
//         console.log("Attempting to reconnect to MQTT broker...");
//         client.connect({
//             onSuccess: function () {
//                 console.log("Reconnected to MQTT broker");
//                 reconnectAttempts = 0; // Reset reconnect attempts on successful connection
//                 client.subscribe('library/borrowed_books');
//             },
//             onFailure: function (error) {
//                 console.error("MQTT reconnection error:", error);
//                 reconnectAttempts++;
//                 setTimeout(reconnect, 5000); // Retry after 5 seconds
//             }
//         });
//     } else {
//         console.error("Max reconnection attempts reached. Please check the broker.");
//     }
// }

// // Handle connection loss
// client.onConnectionLost = function (responseObject) {
//     console.log('Connection lost:', responseObject.errorMessage);
//     reconnect(); // Attempt to reconnect
// };

// // Handle message arrival
// client.onMessageArrived = function (message) {
//     try {
//         const msg = JSON.parse(message.payloadString);
//         console.log(`Received message: ${msg}`);
//     } catch (e) {
//         console.error("Error parsing message payload:", e);
//     }
// };

// // Connect to MQTT broker
// client.connect({
//     onSuccess: function () {
//         console.log("Connected to MQTT broker");
//         client.subscribe('library/borrowed_books');
//     },
//     onFailure: function (error) {
//         console.error("MQTT connection error:", error);
//     }
// });

const client = new Paho.MQTT.Client('localhost', 9001, 'browser-client-addbookissued');
let reconnectAttempts = 0;
const maxReconnectAttempts = 5;

// Function to handle reconnection
function reconnect() {
    if (reconnectAttempts < maxReconnectAttempts) {
        console.log("Attempting to reconnect to MQTT broker...");
        client.connect({
            onSuccess: function () {
                console.log("Reconnected to MQTT broker");
                reconnectAttempts = 0; // Reset reconnect attempts on successful connection
                client.subscribe('library/borrowed_books');
            },
            onFailure: function (error) {
                console.error("MQTT reconnection error:", error);
                reconnectAttempts++;
                setTimeout(reconnect, 5000); // Retry after 5 seconds
            }
        });
    } else {
        console.error("Max reconnection attempts reached. Please check the broker.");
    }
}

// Handle connection loss
client.onConnectionLost = function (responseObject) {
    console.log('Connection lost:', responseObject.errorMessage);
    reconnect(); // Attempt to reconnect
};

// Handle message arrival
client.onMessageArrived = function (message) {
    try {
        const msg = JSON.parse(message.payloadString);
        console.log(`Received message: ${JSON.stringify(msg)}`);

        // Check if message contains expected fields
        if (msg.student_id && msg.book_id && msg.return_date) {
            // Display SweetAlert with the received message
            Swal.fire({
                title: 'New Book Borrowed!',
                text: `Student ID: ${msg.student_id}, Book ID: ${msg.book_id}, Return Date: ${msg.return_date}`,
                icon: 'info',
                confirmButtonText: 'Ok'
            });
        } else {
            console.error("Message missing expected fields:", msg);
        }
    } catch (e) {
        console.error("Error parsing message payload:", e);
    }
};

// Connect to MQTT broker
client.connect({
    onSuccess: function () {
        console.log("Connected to MQTT broker");
        client.subscribe('library/borrowed_books');
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

    // if (!returndate) {
    //     alert("Please select a Return Date.");
    //     return;
    // }

    if (!returndate) {
        Swal.fire({
            title: 'Error!',
            text: 'Please select a Books and Return Date.',
            icon: 'error',
            confirmButtonText: 'Ok'
        });
        return;
    }

    var bookEntry = {
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
    var bookId = row.find("td:first").text();

    borrowedBooks = borrowedBooks.filter((book) => book.id != bookId);
    row.remove();
});

// $("#savebook").click(function () {
//     if (borrowedBooks.length === 0) {
//         alert("No books to save or borrow");
//         return;
//     }

//     console.log("Data to be sent:", JSON.stringify(borrowedBooks));

//     $.ajax({
//         url: "insertborrowedbook.php",
//         method: "POST",
//         contentType: "application/json",
//         data: JSON.stringify(borrowedBooks),
//         success: function (response) {
//             $("#book-list tbody").empty();
//             borrowedBooks = [];

//             // Notify via MQTT
//             borrowedBooks.forEach(function (book) {
//                 const message = JSON.stringify({
//                     action: 'borrow',
//                     student_id: book.tbl_attendance_id,
//                     book_id: book.id,
//                     return_date: book.return_date
//                 });
//                 client.send("library/borrowed_books", message, 0);
//             });
//         },
//     });
// });


// $("#savebook").click(function () {
//     if (borrowedBooks.length === 0) {
//         alert("No books to save or borrow");
//         return;
//     }

//     console.log("Data to be sent:", JSON.stringify(borrowedBooks));

//     $.ajax({
//         url: "insertborrowedbook.php",
//         method: "POST",
//         contentType: "application/json",
//         data: JSON.stringify(borrowedBooks),
//         success: function (response) {
//             $("#book-list tbody").empty();
//             borrowedBooks = [];

//             // Notify via MQTT
//             borrowedBooks.forEach(function (book) {
//                 const message = JSON.stringify({
//                     action: 'borrow',
//                     student_id: book.tbl_attendance_id,
//                     book_id: book.id,
//                     return_date: book.return_date
//                 });
//                 client.send("library/borrowed_books", message, 0);
//             });

//             // Display success message
//             Swal.fire({
//                 title: 'Books Borrowed Successfully!',
//                 text: 'The books have been borrowed and the messages have been sent.',
//                 icon: 'success',
//                 confirmButtonText: 'Ok'
//             });
//         },
//     });
// });

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
            borrowedBooks = [];

            // Display success message
            Swal.fire({
                title: 'Books Borrowed Successfully!',
                text: 'The books have been borrowed and the messages have been sent.',
                icon: 'success',
                confirmButtonText: 'Ok'
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            Swal.fire({
                title: 'Error!',
                text: 'There was an error saving the books: ' + errorThrown,
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        }
    });
});
