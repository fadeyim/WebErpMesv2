using Microsoft.AspNetCore.Mvc;

namespace WebErpMesv2.CSharp.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class HomeController : ControllerBase
    {
        [HttpGet]
        public IActionResult Get()
        {
            return Ok("Welcome to the C# port of WebErpMesv2!");
        }
    }
}
