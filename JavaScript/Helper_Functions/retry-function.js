function retryFunction(fn, attempts = 5, delay = 500) {
    try {
        const result = fn();
        return result;
    } catch (error) {
        if (attempts > 0) {
            console.log(`Error occurred: ${error.message}. Retrying in ${delay}... (${attempts} attempts left)`);
            setTimeout(() => {
                retryFunction(fn, attempts - 1, delay * 2);
            }, delay);
        } else {
            console.log("Max attempts reached. Function failed.");
            return false;
        }
    }
}

// Test:
let counter = 0;
const unstableFunction = () => {
    counter++;

    if (counter < 6) {
        throw new Error("Random failure");
    }

    console.log("Function succeeded!");
};

retryFunction(unstableFunction, 4, 100);