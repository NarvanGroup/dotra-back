<?php

declare(strict_types=1);

namespace App\Models\Vendor;

use App\Enums\Concerns\TranslatableEnum;

enum Industry: string
{
    use TranslatableEnum;

    case NUTS_AND_DRIED_FRUITS = 'nuts-dried-fruits';
    case COSMETICS_AND_HYGIENE = 'cosmetics-hygiene';
    case MUSICAL_INSTRUMENTS = 'musical-instruments';
    case EDUCATION = 'education';
    case TOOLS_AND_EQUIPMENT = 'tools-equipment';
    case ACCOMMODATION_AND_TRAVEL_AGENCY = 'accommodation-travel-agency';
    case ACCESSORIES_AND_JEWELRY = 'accessories-jewelry';
    case PET_SHOP = 'pet-shop';
    case SPORTSWEAR = 'sportswear';
    case CAMPING_AND_SPORTS_EQUIPMENT = 'camping-sports-equipment';
    case ENTERTAINMENT_AND_LEISURE = 'entertainment-leisure';
    case DOMESTIC_TOURS = 'domestic-tours';
    case SERVICES = 'services';
    case HEALTH_SERVICES = 'health-services';
    case WATCHES_AND_GLASSES = 'watches-glasses';
    case HANDICRAFTS_AND_ARTWORKS = 'handicrafts-artworks';
    case GOLD = 'gold';
    case DIGITAL_GADGETS_AND_ACCESSORIES = 'digital-gadgets-accessories';
    case LAPTOP = 'laptop';
    case HOME_ELECTRICAL_APPLIANCES = 'home-electrical-appliances';
    case HOME_DECORATIVE_ITEMS = 'home-decorative-items';
    case NON_ELECTRIC_HOME_APPLIANCES = 'non-electric-home-appliances';
    case CAR_SPARE_PARTS_AND_CONSUMABLES = 'car-spare-parts-consumables';
    case FASHION_AND_APPAREL = 'fashion-apparel';
    case FRESH_FOOD = 'fresh-food';
    case MOBILE_PHONES_AND_TABLETS = 'mobile-tablet';
    case SUPPLEMENTS_MEDICINE_AND_MEDICAL_EQUIPMENT = 'supplements-medicine-medical-equipment';
    case SILVER = 'silver';
    case PERSONAL_ELECTRICAL_DEVICES = 'personal-electrical-devices';
    case FAST_MOVING_CONSUMER_GOODS = 'fmcg';
    case BOOKS_STATIONERY_AND_TOYS = 'books-stationery-toys';
    case BABY_AND_KIDS = 'baby-kids';
    case BAGS_AND_SHOES = 'bags-shoes';

    /**
     * Commission percentage associated with each industry.
     */
    public function commissionRate(): int
    {
        return match ($this) {
            self::GOLD,
            self::FRESH_FOOD => 10,
            self::LAPTOP,
            self::MOBILE_PHONES_AND_TABLETS => 9,
            self::DIGITAL_GADGETS_AND_ACCESSORIES,
            self::HOME_ELECTRICAL_APPLIANCES,
            self::PERSONAL_ELECTRICAL_DEVICES => 11,
            self::NUTS_AND_DRIED_FRUITS,
            self::COSMETICS_AND_HYGIENE,
            self::MUSICAL_INSTRUMENTS,
            self::ACCOMMODATION_AND_TRAVEL_AGENCY,
            self::CAMPING_AND_SPORTS_EQUIPMENT,
            self::WATCHES_AND_GLASSES,
            self::HOME_DECORATIVE_ITEMS,
            self::CAR_SPARE_PARTS_AND_CONSUMABLES,
            self::SUPPLEMENTS_MEDICINE_AND_MEDICAL_EQUIPMENT,
            self::FAST_MOVING_CONSUMER_GOODS => 12,
            self::TOOLS_AND_EQUIPMENT,
            self::SPORTSWEAR,
            self::DOMESTIC_TOURS,
            self::FASHION_AND_APPAREL,
            self::NON_ELECTRIC_HOME_APPLIANCES,
            self::BOOKS_STATIONERY_AND_TOYS,
            self::BABY_AND_KIDS,
            self::BAGS_AND_SHOES => 13,
            self::HANDICRAFTS_AND_ARTWORKS => 14,
            self::EDUCATION,
            self::ACCESSORIES_AND_JEWELRY,
            self::PET_SHOP,
            self::ENTERTAINMENT_AND_LEISURE,
            self::SERVICES,
            self::HEALTH_SERVICES,
            self::SILVER => 15,
        };
    }

    public static function translationPrefix(): string
    {
        return 'models.vendors.industries';
    }
}
