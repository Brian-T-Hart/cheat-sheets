
function try_try_again(attempting, attempts = 3, delay = 250) {
    if (attempts > 0) {
        console.log(`Remaining attempts: ${attempts}`);
        console.log(`Current delay: ${delay}ms`);
        attempts--;
        delay = delay * 2; // Exponential backoff

        if (attempting()) {
            console.log('Success!');
            return;
        }
        
        else {
            setTimeout(() => {
                console.log('Delay in setTimeout: ' + delay + 'ms');
                try_try_again(attempting, attempts, delay);
            }, delay);
        }
    } else {
        console.log('No more attempts');
    }
}

function do_something() {
    console.log('Doing something...');
    return false;
}

try_try_again(do_something);