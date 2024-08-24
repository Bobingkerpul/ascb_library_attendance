// $('.edit').click(function() {
//     // alert();
//     var request = $.ajax({
//         url: "issuedbook.php",
//         method: "GET",
//         data: {
//             id: this.value
//         },
//         dataType: "json"
//     });

//     request.done(function(msg) {
//         // console.log(msg);
//         $("#id").val(msg.tbl_attendance_id);
//         $("#student_name").val(msg.student_name);
//         $("#course_m").val(msg.course_section);
//         $("#time_in_m").val(msg.time_in);
//         $("#time_out_m").val(msg.time_out);
//         $("#log_date_m").val(msg.log_date);
//     });
// });