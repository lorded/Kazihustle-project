# Mobi Power

Mobi Power is a USSD-based application designed to facilitate the payment of electricity bills and generate tokens that users can input into their meter devices to restore electricity. The application integrates with M-Pesa Paybill services and an electricity token device API, providing a seamless and efficient way for users to manage their electricity needs.

## Features
- **USSD Interface:** Easy-to-use interface for paying electricity bills via a USSD code.
- **M-Pesa Integration:** Secure and reliable payment through M-Pesa Paybill.
- **Token Generation:** After payment, users receive electricity tokens which they can use to recharge their electricity meters.
- **Real-Time Updates:** Instant confirmation of payments and token generation.

## Technology Stack
- **Backend:** MySQL, PHP ,LARAVEL
- **Payment Integration:** M-Pesa Paybill API
- **Electricity Token API:** Custom API for generating tokens

## Installation

1. Clone the repository:
   
   git clone https://github.com/lorded/mobi-power.git

    Navigate to the project directory:


Set up your files with the necessary credentials:

    M-Pesa API keys READ HERE  https://developer.safaricom.co.ke/
    Database credentials
    Stron Token API credentials
    Africas Talking API READ HERE https://developers.africastalking.com/docs/ussd/handle_sessions
    

Run the application:


Usage

    Dial the USSD code to access the Mobi Power interface.
    Select the option to pay for electricity.
    Enter your meter number and the amount.
    Confirm the payment via M-Pesa.
    Receive a token and input it into your meter device to restore electricity.

Contributing

Contributions are welcome! Please follow the contributing guidelines.
License

This project is licensed under the MIT License - see the LICENSE file for details.
Authors

    Dishon Migwi Maina - lorded - Creator and Developer - dishonmigwi6@gmail.com





