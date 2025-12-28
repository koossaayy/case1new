<?php
namespace App\Livewire\Tools;

use Livewire\Component;

class CompanyGenerator extends Component
{
    public static $slug         = 'random-company';
    public static $title        = 'Random Company Generator';
    public static $description  = 'Generate realistic company profiles with names, industries, slogans, addresses, and contact details. Ideal for mockups, testing CRM systems, or creating sample business data.';
    public static $keywords     = 'random company generator, fake company generator, company name generator, business name generator, mock company data, company profile generator, fake business generator, test company data, company data seeding, crm test data';
    public static $relatedTools = ['person-generator', 'address-generator', 'phone-number-generator', 'payment-generator'];

    public $companyName;
    public $legalName;
    public $industry;
    public $subIndustry;
    public $companySize;
    public $foundedYear;

    // Branding
    public $tagline;
    public $slogan;
    public $missionStatement;
    public $vision;

    // Contact & Web
    public $domain;
    public $email;
    public $phone;
    public $website;

    // Location
    public $headquarters;
    public $city;
    public $country;
    public $address;

    // Design
    public $primaryColor;
    public $secondaryColor;
    public $logoStyle;

    // Business details
    public $businessModel;
    public $targetMarket;
    public $revenue;
    public $employees;

    // Social
    public $twitter;
    public $linkedin;
    public $facebook;

    private $industries = [
        'Technology'      => ['SaaS', 'Cloud Computing', 'Cybersecurity', 'AI/ML', 'Mobile Apps', 'Blockchain', 'IoT', 'DevOps'],
        'E-commerce'      => ['Fashion Retail', 'Electronics', 'Home & Garden', 'Beauty & Cosmetics', 'Sports Equipment', 'Digital Products'],
        'Finance'         => ['Fintech', 'Banking', 'Insurance', 'Investment', 'Cryptocurrency', 'Payment Processing', 'Wealth Management'],
        'Healthcare'      => ['Telemedicine', 'Medical Devices', 'Pharmaceuticals', 'Health Tech', 'Wellness', 'Biotech'],
        'Education'       => ['EdTech', 'Online Learning', 'Corporate Training', 'Language Learning', 'K-12 Education', 'Higher Education'],
        'Marketing'       => ['Digital Marketing', 'SEO Services', 'Social Media', 'Content Marketing', 'Email Marketing', 'Advertising'],
        'Real Estate'     => ['Property Management', 'Commercial Real Estate', 'PropTech', 'Construction', 'Architecture'],
        'Food & Beverage' => ['Restaurant Tech', 'Food Delivery', 'Catering', 'Organic Foods', 'Coffee & Tea', 'Meal Kits'],
        'Entertainment'   => ['Streaming Services', 'Gaming', 'Music Production', 'Event Management', 'Content Creation'],
        'Manufacturing'   => ['Industrial Equipment', 'Consumer Electronics', 'Automotive Parts', 'Green Energy', ' 3D Printing'],
        'Consulting'      => ['Business Strategy', 'IT Consulting', 'HR Services', 'Legal Tech', 'Management Consulting'],
        'Logistics'       => ['Supply Chain', 'Freight', 'Last Mile Delivery', 'Warehouse Management', 'Fleet Management'],
    ];

    private $companySuffixes = ['Inc', 'LLC', 'Corp', 'Ltd', 'Group', 'Solutions', 'Technologies', 'Systems', 'Labs', 'Studios', 'Ventures', 'Partners'];

    private $businessModels = ['B2B', 'B2C', 'B2B2C', 'Marketplace', 'SaaS', 'Subscription', 'Freemium', 'Enterprise', 'SMB Focus', 'E-commerce'];

    private $companySizes = [
        'Startup'          => '1-10',
        'Small Business'   => '11-50',
        'Medium Business'  => '51-200',
        'Large Enterprise' => '201-1000',
        'Corporation'      => '1000+',
    ];

    private $logoStyles = ['Minimalist', 'Modern', 'Geometric', 'Abstract', 'Lettermark', 'Wordmark', 'Badge', 'Emblem', 'Icon-based'];

    public function generate()
    {
        // Pick industry
        $industryKeys      = array_keys($this->industries);
        $this->industry    = $industryKeys[array_rand($industryKeys)];
        $this->subIndustry = $this->industries[$this->industry][array_rand($this->industries[$this->industry])];

        // Generate company name
        $this->companyName = $this->generateCompanyName();
        $this->legalName   = $this->companyName . ' ' . $this->companySuffixes[array_rand($this->companySuffixes)];

        // Generate domain and digital presence
        $domainName    = strtolower(str_replace([' ', '&', ','], ['', 'and', ''], $this->companyName));
        $domainName    = preg_replace('/[^a-z0-9]/', '', $domainName);
        $this->domain  = $domainName . '.' . $this->getRandomDomainExtension();
        $this->website = 'https://' . $this->domain;
        $this->email   = 'contact@' . $this->domain;

        // Social media
        $socialHandle   = strtolower(preg_replace('/[^a-z0-9]/', '', $this->companyName));
        $this->twitter  = '@' . $socialHandle;
        $this->linkedin = 'linkedin.com/company/' . $socialHandle;
        $this->facebook = 'facebook.com/' . $socialHandle;

        // Generate branding
        $this->tagline          = $this->generateTagline();
        $this->slogan           = $this->generateSlogan();
        $this->missionStatement = $this->generateMissionStatement();
        $this->vision           = $this->generateVision();

        // Company details
        $sizeKey             = array_rand($this->companySizes);
        $this->companySize   = $sizeKey;
        $this->employees     = $this->companySizes[$sizeKey];
        $this->foundedYear   = rand(1995, 2023);
        $this->businessModel = $this->businessModels[array_rand($this->businessModels)];
        $this->targetMarket  = $this->generateTargetMarket();
        $this->revenue       = $this->generateRevenue($sizeKey);

        // Location
        $this->city         = fake()->city();
        $this->country      = fake()->country();
        $this->headquarters = $this->city . ', ' . $this->country;
        $this->address      = fake()->streetAddress() . ', ' . $this->city;
        $this->phone        = fake()->phoneNumber();

        // Design
        $this->primaryColor   = $this->generateBrandColor();
        $this->secondaryColor = $this->generateBrandColor();
        $this->logoStyle      = $this->logoStyles[array_rand($this->logoStyles)];

        // Track usage
        // $this->trackUsage([
        //     'generated' => [
        //         'company'  => $this->companyName,
        //         'industry' => $this->industry,
        //         'domain'   => $this->domain,
        //     ],
        // ]);
    }

    private function generateCompanyName()
    {
        $patterns = [
            // Tech style names
            fn() => $this->getTechPrefix() . $this->getTechSuffix(),
            // Descriptive names
            fn() => $this->getAdjective() . ' ' . $this->getIndustryNoun(),
            // Portmanteau style
            fn() => $this->createPortmanteau(),
            // Classic business
            fn() => fake()->lastName() . ' ' . $this->companySuffixes[array_rand($this->companySuffixes)],
            // Modern tech
            fn() => $this->getTechWord() . $this->getTechEnding(),
        ];

        return $patterns[array_rand($patterns)]();
    }

    private function getTechPrefix()
    {
        $prefixes = ['Cloud', 'Data', 'Smart', 'Tech', 'Digital', 'Cyber', 'Net', 'Web', 'App', 'Micro', 'Nano', 'Meta', 'Quantum', 'Neural', 'Sync', 'Flux', 'Nexus', 'Pixel', 'Byte', 'Code'];
        return $prefixes[array_rand($prefixes)];
    }

    private function getTechSuffix()
    {
        $suffixes = ['Flow', 'Sync', 'Hub', 'Base', 'Core', 'Wave', 'Pulse', 'Link', 'Path', 'Nest', 'Spot', 'Port', 'Grid', 'Stack', 'Forge', 'Mesh', 'Stream', 'Vault'];
        return $suffixes[array_rand($suffixes)];
    }

    private function getTechWord()
    {
        $words = ['Inno', 'Opti', 'Verti', 'Hori', 'Modu', 'Solu', 'Strate', 'Maximi', 'Optimi', 'Digiti'];
        return $words[array_rand($words)];
    }

    private function getTechEnding()
    {
        $endings = ['fy', 'ly', 'io', 'ity', 'ze', 'wise', 'tech', 'hub', 'lab'];
        return $endings[array_rand($endings)];
    }

    private function getAdjective()
    {
        $adjectives = ['Agile', 'Swift', 'Bright', 'Prime', 'Peak', 'Elite', 'Noble', 'Vital', 'Core', 'True', 'Bold', 'Pure', 'Wise', 'Global', 'United', 'Stellar', 'Summit', 'Apex', 'Zenith'];
        return $adjectives[array_rand($adjectives)];
    }

    private function getIndustryNoun()
    {
        $nouns = ['Solutions', 'Systems', 'Dynamics', 'Innovations', 'Technologies', 'Partners', 'Group', 'Ventures', 'Capital', 'Industries', 'Enterprises', 'Networks', 'Global', 'Collective'];
        return $nouns[array_rand($nouns)];
    }

    private function createPortmanteau()
    {
        $part1 = ['Inno', 'Tech', 'Digi', 'Smart', 'Cloud', 'Data', 'Soft', 'Cyber'];
        $part2 = ['vate', 'ware', 'tal', 'core', 'base', 'flow', 'hub', 'link'];
        return $part1[array_rand($part1)] . $part2[array_rand($part2)];
    }

    private function generateTagline()
    {
        $templates = [
            "Empowering {target} through {value}",
            "The future of {industry}",
            "{value} you can trust",
            "Where {value} meets {value2}",
            "Transforming {industry} with {technology}",
            "{action} the way you {activity}",
            "Built for {target}",
            "Your partner in {industry}",
            "{value} at scale",
            "Innovating {industry} since {year}",
        ];

        $replacements = [
            '{target}'     => ['businesses', 'enterprises', 'teams', 'innovators', 'leaders', 'professionals', 'startups', 'organizations'],
            '{value}'      => ['innovation', 'excellence', 'quality', 'reliability', 'trust', 'performance', 'efficiency', 'growth'],
            '{value2}'     => ['technology', 'expertise', 'passion', 'precision', 'simplicity', 'speed'],
            '{industry}'   => [strtolower($this->industry), strtolower($this->subIndustry)],
            '{technology}' => ['AI', 'automation', 'analytics', 'cloud technology', 'innovation', 'smart solutions'],
            '{action}'     => ['Revolutionize', 'Transform', 'Simplify', 'Accelerate', 'Optimize', 'Streamline', 'Elevate'],
            '{activity}'   => ['work', 'grow', 'succeed', 'innovate', 'scale', 'compete', 'connect'],
            '{year}'       => [$this->foundedYear],
        ];

        $template = $templates[array_rand($templates)];

        foreach ($replacements as $key => $values) {
            if (strpos($template, $key) !== false) {
                $template = str_replace($key, $values[array_rand($values)], $template);
            }
        }

        return $template;
    }

    private function generateSlogan()
    {
        $slogans = [
            "Innovation at every step",
            "Excellence delivered",
            "Your success, our mission",
            "Building tomorrow, today",
            "Where ideas become reality",
            "Powered by innovation",
            "Simply better",
            "Leading the way forward",
            "Performance you can measure",
            "The smarter choice",
            "Connecting possibilities",
            "Beyond expectations",
            "Driving digital transformation",
            "Solutions that scale",
            "Engineering the future",
        ];

        return $slogans[array_rand($slogans)];
    }

    private function generateMissionStatement()
    {
        $templates = [
            "To empower {target} with innovative {industry} solutions that drive sustainable growth and create lasting value.",
            "Our mission is to revolutionize {industry} by delivering cutting-edge {technology} that transforms how {target} operate.",
            "We exist to provide {target} with world-class {value} through exceptional {industry} services and unwavering commitment to excellence.",
            "Dedicated to building the future of {industry} through {value}, {value2}, and customer-centric solutions.",
            "To be the trusted partner for {target} seeking to leverage {technology} for competitive advantage and business success.",
        ];

        $replacements = [
            '{target}'     => ['businesses', 'enterprises', 'organizations', 'industry leaders', 'forward-thinking companies'],
            '{industry}'   => [strtolower($this->industry), strtolower($this->subIndustry)],
            '{technology}' => ['technology', 'innovation', 'data-driven insights', 'AI-powered tools', 'scalable platforms'],
            '{value}'      => ['innovation', 'integrity', 'excellence', 'collaboration', 'transparency'],
            '{value2}'     => ['quality', 'reliability', 'customer focus', 'continuous improvement', 'sustainable practices'],
        ];

        $template = $templates[array_rand($templates)];

        foreach ($replacements as $key => $values) {
            if (strpos($template, $key) !== false) {
                $template = str_replace($key, $values[array_rand($values)], $template);
            }
        }

        return $template;
    }

    private function generateVision()
    {
        $visions = [
            "To become the global leader in {industry}, setting new standards for innovation and customer success.",
            "Creating a world where {target} can unlock their full potential through intelligent {industry} solutions.",
            "To shape the future of {industry} by pioneering breakthrough technologies and sustainable business practices.",
            "Building a future where every {target} has access to world-class {industry} solutions.",
            "To be recognized worldwide as the most trusted and innovative {industry} company, driving positive change across industries.",
        ];

        $template = $visions[array_rand($visions)];
        $template = str_replace('{industry}', strtolower($this->industry), $template);
        $template = str_replace('{target}', ['organization', 'business', 'enterprise', 'company'][array_rand(['organization', 'business', 'enterprise', 'company'])], $template);

        return $template;
    }

    private function generateTargetMarket()
    {
        $markets = [
            'Small to Medium Businesses',
            'Enterprise Companies',
            'Startups & Scaleups',
            'Fortune 500 Companies',
            'Mid-Market Enterprises',
            'Government & Public Sector',
            'Healthcare Organizations',
            'Financial Institutions',
            'Educational Institutions',
            'Technology Companies',
            'Retail & E-commerce',
            'Manufacturing Sector',
            'Professional Services',
        ];

        return $markets[array_rand($markets)];
    }

    private function generateRevenue($sizeKey)
    {
        $ranges = [
            'Startup'          => ['$100K - $500K', '$500K - $1M', 'Pre-revenue', '$50K - $250K'],
            'Small Business'   => ['$1M - $5M', '$5M - $10M', '$500K - $2M'],
            'Medium Business'  => ['$10M - $50M', '$50M - $100M', '$25M - $75M'],
            'Large Enterprise' => ['$100M - $500M', '$500M - $1B', '$250M - $750M'],
            'Corporation'      => ['$1B - $5B', '$5B - $10B', '$10B+', '$2B - $8B'],
        ];

        return $ranges[$sizeKey][array_rand($ranges[$sizeKey])];
    }

    private function generateBrandColor()
    {
        $brandColors = [
            '#2563EB', '#7C3AED', '#DC2626', '#059669', '#EA580C',
            '#0891B2', '#4F46E5', '#DB2777', '#65A30D', '#0284C7',
            '#6366F1', '#8B5CF6', '#EC4899', '#F59E0B', '#10B981',
            '#14B8A6', '#3B82F6', '#A855F7', '#EF4444', '#F97316',
        ];

        return $brandColors[array_rand($brandColors)];
    }

    private function getRandomDomainExtension()
    {
        $extensions = ['com', 'io', 'co', 'ai', 'tech', 'app', 'dev', 'cloud', 'net', 'digital'];
        $weights    = [50, 15, 10, 8, 5, 4, 3, 2, 2, 1]; // .com is most common

        $rand = rand(1, array_sum($weights));
        $sum  = 0;

        foreach ($weights as $i => $weight) {
            $sum += $weight;
            if ($rand <= $sum) {
                return $extensions[$i];
            }
        }

        return 'com';
    }

    public function render()
    {
        return view('livewire.tools.company-generator');
    }
}
