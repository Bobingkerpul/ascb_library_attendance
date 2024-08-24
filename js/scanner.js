 let scanner;

function startScanner() {
    scanner = new Instascan.Scanner({
        video: document.getElementById('interactive')
    });

    scanner.addListener('scan', function(content) {
        $("#detected-qr-code").val(content);
        console.log(content);
        scanner.stop();
        document.querySelector(".qr-detected-container").style.display = '';
        document.querySelector(".scanner-con").style.display = 'none';
    });

    Instascan.Camera.getCameras()
        .then(function(cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
            } else {
                console.error('No cameras found.');
                alert('No cameras found.');
            }
        })
        .catch(function(err) {
            console.error('Camera access error:', err);
            alert('Camera access error: ' + err);
        });
}

document.addEventListener('DOMContentLoaded', startScanner);

function deleteAttendance(id) {
    if (confirm("Do you want to remove this attendance?")) {
        window.location = "./endpoint/delete-attendance.php?attendance=" + id;
    }
}